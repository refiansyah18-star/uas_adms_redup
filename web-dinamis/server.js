const express = require('express');
const session = require('express-session');
const bcrypt = require('bcryptjs');
const mysql = require('mysql2/promise');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

const dbConfig = {
  host: process.env.DB_HOST || 'localhost',
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER || 'finchat_user',
  password: process.env.DB_PASSWORD || 'finchat_password',
  database: process.env.DB_NAME || 'finchat_uas',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  dateStrings: true
};

let pool;

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.static(path.join(__dirname, 'public')));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'finchat_uas_session_secret',
    resave: false,
    saveUninitialized: false,
    cookie: {
      httpOnly: true,
      sameSite: 'lax',
      maxAge: 1000 * 60 * 60 * 8
    }
  })
);

app.use((req, res, next) => {
  res.locals.user = req.session.user || null;
  res.locals.currentPath = req.path;
  res.locals.flash = req.session.flash || null;
  delete req.session.flash;
  res.locals.formatRupiah = formatRupiah;
  res.locals.formatDateID = formatDateID;
  res.locals.formatDateShortID = formatDateShortID;
  next();
});

function setFlash(req, type, message) {
  req.session.flash = { type, message };
}

function requireAuth(req, res, next) {
  if (!req.session.user) {
    setFlash(req, 'info', 'Silakan masuk terlebih dahulu untuk membuka dashboard FinChat.');
    return res.redirect('/masuk');
  }
  next();
}

function formatRupiah(value) {
  const number = Number(value || 0);
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(number);
}

function formatDateID(dateInput) {
  const date = dateInput ? new Date(dateInput) : new Date();
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  }).format(date);
}

function formatDateShortID(dateInput) {
  const date = dateInput ? new Date(dateInput) : new Date();
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short'
  }).format(date);
}

function toDateInputValue(date = new Date()) {
  const yyyy = date.getFullYear();
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const dd = String(date.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}

function currentMonthInput() {
  const date = new Date();
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
}

function monthRange(monthInput) {
  const safeMonth = /^\d{4}-\d{2}$/.test(monthInput || '') ? monthInput : currentMonthInput();
  const [year, month] = safeMonth.split('-').map(Number);
  const start = new Date(year, month - 1, 1);
  const end = new Date(year, month, 1);
  return { start: toDateInputValue(start), end: toDateInputValue(end), month: safeMonth };
}

function monthLabel(monthInput) {
  const { start } = monthRange(monthInput);
  return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(new Date(start));
}

function normalizeNumber(raw) {
  if (!raw) return 0;
  const cleaned = String(raw).replace(/\./g, '').replace(',', '.');
  return Number(cleaned);
}

function parseAmount(text) {
  const amountPattern = /(\d+(?:[\.,]\d+)?)\s*(juta|jt|ribu|rb|k)?/i;
  const match = text.match(amountPattern);
  if (!match) return 0;

  let amount = normalizeNumber(match[1]);
  const suffix = (match[2] || '').toLowerCase();

  if (['rb', 'ribu', 'k'].includes(suffix)) amount *= 1000;
  if (['jt', 'juta'].includes(suffix)) amount *= 1000000;

  return Math.round(amount);
}

const categoryRules = [
  {
    name: 'Makanan & Minuman',
    type: 'expense',
    keywords: ['kopi', 'makan', 'nasi', 'ayam', 'seblak', 'bakso', 'mie', 'minum', 'cafe', 'resto', 'jajan']
  },
  {
    name: 'Transportasi',
    type: 'expense',
    keywords: ['grab', 'gojek', 'ojek', 'bensin', 'parkir', 'tol', 'angkot', 'bus', 'kereta', 'transport']
  },
  {
    name: 'Tempat Tinggal',
    type: 'expense',
    keywords: ['kos', 'kontrakan', 'listrik', 'air', 'internet', 'wifi', 'sewa']
  },
  {
    name: 'Hiburan',
    type: 'expense',
    keywords: ['netflix', 'spotify', 'bioskop', 'game', 'konser', 'hiburan']
  },
  {
    name: 'Belanja',
    type: 'expense',
    keywords: ['baju', 'sepatu', 'skincare', 'barang', 'belanja', 'toko', 'shopee', 'tokopedia']
  },
  {
    name: 'Pendidikan',
    type: 'expense',
    keywords: ['buku', 'kuliah', 'kursus', 'kelas', 'fotokopi', 'print', 'pendidikan']
  },
  {
    name: 'Pemasukan',
    type: 'income',
    keywords: ['gaji', 'transfer', 'bonus', 'honor', 'upah', 'dapat', 'masuk', 'pemasukan', 'uang saku']
  }
];

function detectType(text) {
  const lower = text.toLowerCase();
  const incomeWords = ['gaji', 'dapat', 'menerima', 'transfer masuk', 'bonus', 'pemasukan', 'honor', 'upah', 'uang saku'];
  if (incomeWords.some((word) => lower.includes(word))) return 'income';
  return 'expense';
}

function detectDate(text) {
  const lower = text.toLowerCase();
  const date = new Date();
  if (lower.includes('kemarin')) date.setDate(date.getDate() - 1);
  if (lower.includes('lusa')) date.setDate(date.getDate() + 2);
  if (lower.includes('besok')) date.setDate(date.getDate() + 1);
  return toDateInputValue(date);
}

function cleanDescription(text) {
  return text
    .replace(/\d+(?:[\.,]\d+)?\s*(juta|jt|ribu|rb|k)?/gi, '')
    .replace(/\b(tadi|hari ini|kemarin|besok|lusa|sebesar|senilai|rp)\b/gi, '')
    .replace(/\s+/g, ' ')
    .trim() || 'Transaksi via chat';
}

function parseChatInput(text) {
  const input = String(text || '').trim();
  const lower = input.toLowerCase();
  const amount = parseAmount(lower);
  const type = detectType(lower);

  let categoryName = type === 'income' ? 'Pemasukan' : 'Lainnya';
  let matchedKeywords = 0;

  for (const rule of categoryRules) {
    const found = rule.keywords.filter((keyword) => lower.includes(keyword));
    if (found.length > matchedKeywords && (rule.type === type || rule.name === 'Pemasukan')) {
      matchedKeywords = found.length;
      categoryName = rule.name;
    }
  }

  const confidence = Math.min(0.98, 0.48 + (amount > 0 ? 0.25 : 0) + (matchedKeywords > 0 ? 0.2 : 0) + (input.length > 6 ? 0.05 : 0));

  return {
    originalText: input,
    type,
    amount,
    categoryName,
    description: cleanDescription(input),
    transactionDate: detectDate(input),
    confidence
  };
}

async function connectWithRetry(maxRetries = 30) {
  for (let attempt = 1; attempt <= maxRetries; attempt += 1) {
    try {
      pool = mysql.createPool(dbConfig);
      await pool.query('SELECT 1');
      console.log('Database MariaDB tersambung.');
      return;
    } catch (error) {
      console.log(`Menunggu database siap... percobaan ${attempt}/${maxRetries}`);
      await new Promise((resolve) => setTimeout(resolve, 2000));
    }
  }
  throw new Error('Database tidak bisa dihubungi setelah beberapa percobaan.');
}

async function runBootstrap() {
  await pool.query(`
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(150) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  `);

  await pool.query(`
    CREATE TABLE IF NOT EXISTS categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL UNIQUE,
      type ENUM('income', 'expense') NOT NULL DEFAULT 'expense',
      icon VARCHAR(20) DEFAULT 'wallet',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  `);

  await pool.query(`
    CREATE TABLE IF NOT EXISTS transactions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      category_id INT NOT NULL,
      type ENUM('income', 'expense') NOT NULL,
      amount INT NOT NULL,
      description VARCHAR(255) NOT NULL,
      transaction_date DATE NOT NULL,
      source_text VARCHAR(255),
      confidence_score DECIMAL(4,2) DEFAULT 0.00,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  `);

  await pool.query(`
    CREATE TABLE IF NOT EXISTS budgets (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      category_id INT NOT NULL,
      monthly_limit INT NOT NULL,
      month_year CHAR(7) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY unique_budget (user_id, category_id, month_year),
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
      FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  `);

  for (const rule of categoryRules) {
    const icon = {
      'Makanan & Minuman': 'restaurant',
      Transportasi: 'directions_car',
      'Tempat Tinggal': 'home',
      Hiburan: 'movie',
      Belanja: 'shopping_bag',
      Pendidikan: 'school',
      Pemasukan: 'payments'
    }[rule.name] || 'wallet';
    await pool.query('INSERT IGNORE INTO categories (name, type, icon) VALUES (?, ?, ?)', [rule.name, rule.type, icon]);
  }
  await pool.query('INSERT IGNORE INTO categories (name, type, icon) VALUES (?, ?, ?)', ['Lainnya', 'expense', 'wallet']);

  const [users] = await pool.query('SELECT id FROM users WHERE email = ?', ['demo@finchat.local']);
  let demoUserId = users[0]?.id;

  if (!demoUserId) {
    const passwordHash = await bcrypt.hash('password123', 10);
    const [result] = await pool.query('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', [
      'Ahmad Refiansyah',
      'demo@finchat.local',
      passwordHash
    ]);
    demoUserId = result.insertId;
  }

  const [[transactionCount]] = await pool.query('SELECT COUNT(*) AS total FROM transactions WHERE user_id = ?', [demoUserId]);
  if (transactionCount.total === 0) {
    const samples = [
      'dapat uang saku 1200rb',
      'tadi beli kopi 25rb',
      'bayar kos 800rb',
      'makan ayam geprek 18000',
      'bensin motor 35000',
      'beli buku kuliah 75000',
      'langganan spotify 55000'
    ];
    for (const sample of samples) {
      const parsed = parseChatInput(sample);
      const categoryId = await findCategoryId(parsed.categoryName, parsed.type);
      await pool.query(
        `INSERT INTO transactions
        (user_id, category_id, type, amount, description, transaction_date, source_text, confidence_score)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
        [demoUserId, categoryId, parsed.type, parsed.amount, parsed.description, parsed.transactionDate, parsed.originalText, parsed.confidence]
      );
    }
  }

  const [[budgetCount]] = await pool.query('SELECT COUNT(*) AS total FROM budgets WHERE user_id = ? AND month_year = ?', [demoUserId, currentMonthInput()]);
  if (budgetCount.total === 0) {
    const budgetSeeds = [
      ['Makanan & Minuman', 900000],
      ['Transportasi', 450000],
      ['Tempat Tinggal', 1000000],
      ['Hiburan', 250000]
    ];
    for (const [categoryName, monthlyLimit] of budgetSeeds) {
      const categoryId = await findCategoryId(categoryName, 'expense');
      await pool.query('INSERT IGNORE INTO budgets (user_id, category_id, monthly_limit, month_year) VALUES (?, ?, ?, ?)', [
        demoUserId,
        categoryId,
        monthlyLimit,
        currentMonthInput()
      ]);
    }
  }
}

async function findCategoryId(categoryName, type = 'expense') {
  const [rows] = await pool.query('SELECT id FROM categories WHERE name = ? LIMIT 1', [categoryName]);
  if (rows.length) return rows[0].id;
  const fallback = type === 'income' ? 'Pemasukan' : 'Lainnya';
  const [fallbackRows] = await pool.query('SELECT id FROM categories WHERE name = ? LIMIT 1', [fallback]);
  return fallbackRows[0].id;
}

async function getCategories(type = null) {
  if (type) {
    const [rows] = await pool.query('SELECT * FROM categories WHERE type = ? ORDER BY name ASC', [type]);
    return rows;
  }
  const [rows] = await pool.query('SELECT * FROM categories ORDER BY type ASC, name ASC');
  return rows;
}

async function getDashboardData(userId, selectedMonth = currentMonthInput()) {
  const { start, end, month } = monthRange(selectedMonth);

  const [[summary]] = await pool.query(
    `SELECT
      COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income,
      COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS expense
    FROM transactions
    WHERE user_id = ? AND transaction_date >= ? AND transaction_date < ?`,
    [userId, start, end]
  );

  const [recentTransactions] = await pool.query(
    `SELECT t.*, c.name AS category_name, c.icon
    FROM transactions t
    JOIN categories c ON c.id = t.category_id
    WHERE t.user_id = ?
    ORDER BY t.transaction_date DESC, t.id DESC
    LIMIT 5`,
    [userId]
  );

  const [categoryStats] = await pool.query(
    `SELECT c.name, COALESCE(SUM(t.amount), 0) AS total
    FROM transactions t
    JOIN categories c ON c.id = t.category_id
    WHERE t.user_id = ? AND t.type = 'expense' AND t.transaction_date >= ? AND t.transaction_date < ?
    GROUP BY c.id, c.name
    ORDER BY total DESC
    LIMIT 5`,
    [userId, start, end]
  );

  const [budgets] = await pool.query(
    `SELECT b.*, c.name AS category_name,
      COALESCE(SUM(t.amount), 0) AS used_amount
    FROM budgets b
    JOIN categories c ON c.id = b.category_id
    LEFT JOIN transactions t ON t.user_id = b.user_id
      AND t.category_id = b.category_id
      AND t.type = 'expense'
      AND t.transaction_date >= ?
      AND t.transaction_date < ?
    WHERE b.user_id = ? AND b.month_year = ?
    GROUP BY b.id, c.name
    ORDER BY used_amount DESC`,
    [start, end, userId, month]
  );

  const balance = Number(summary.income || 0) - Number(summary.expense || 0);
  const insights = buildInsights(summary, categoryStats, budgets);

  return {
    month,
    monthLabel: monthLabel(month),
    summary: {
      income: Number(summary.income || 0),
      expense: Number(summary.expense || 0),
      balance
    },
    recentTransactions,
    categoryStats,
    budgets,
    insights
  };
}

function buildInsights(summary, categoryStats, budgets) {
  const insights = [];
  const topCategory = categoryStats[0];
  if (topCategory) {
    insights.push({
      title: 'Kategori paling boros',
      message: `${topCategory.name} menjadi pengeluaran terbesar bulan ini: ${formatRupiah(topCategory.total)}.`,
      tone: 'warning'
    });
  }

  const criticalBudget = budgets.find((budget) => Number(budget.used_amount) >= Number(budget.monthly_limit) * 0.8);
  if (criticalBudget) {
    const percent = Math.round((Number(criticalBudget.used_amount) / Number(criticalBudget.monthly_limit)) * 100);
    insights.push({
      title: 'Budget hampir habis',
      message: `Budget ${criticalBudget.category_name} sudah terpakai ${percent}%. Cek lagi pengeluaran harianmu.`,
      tone: 'danger'
    });
  }

  if (Number(summary.expense) === 0) {
    insights.push({
      title: 'Mulai catat transaksi',
      message: 'Coba tulis “beli kopi 25rb” agar dashboard mulai terisi otomatis.',
      tone: 'success'
    });
  } else {
    insights.push({
      title: 'Catatan FinChat',
      message: 'Input via chat aktif. Kamu juga bisa tanya “sisa budget makan berapa?”.',
      tone: 'success'
    });
  }

  return insights.slice(0, 3);
}

async function saveParsedTransaction(userId, parsed) {
  if (!parsed.amount || parsed.amount <= 0) {
    throw new Error('Nominal belum terbaca. Coba tulis seperti “beli kopi 25rb”.');
  }
  const categoryId = await findCategoryId(parsed.categoryName, parsed.type);
  await pool.query(
    `INSERT INTO transactions
    (user_id, category_id, type, amount, description, transaction_date, source_text, confidence_score)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    [
      userId,
      categoryId,
      parsed.type,
      parsed.amount,
      parsed.description,
      parsed.transactionDate,
      parsed.originalText,
      parsed.confidence
    ]
  );
}

app.get('/', (req, res) => {
  res.render('beranda', {
    title: 'FinChat - Catat Keuangan Lewat Chat'
  });
});

app.get('/masuk', (req, res) => {
  res.render('masuk', { title: 'Masuk - FinChat' });
});

app.post('/masuk', async (req, res) => {
  const { email, password } = req.body;
  const [rows] = await pool.query('SELECT * FROM users WHERE email = ? LIMIT 1', [email]);
  const user = rows[0];

  if (!user || !(await bcrypt.compare(password, user.password_hash))) {
    setFlash(req, 'error', 'Email atau kata sandi belum sesuai.');
    return res.redirect('/masuk');
  }

  req.session.user = { id: user.id, name: user.name, email: user.email };
  setFlash(req, 'success', 'Berhasil masuk. Dashboard FinChat siap digunakan.');
  return res.redirect('/dashboard');
});

app.get('/daftar', (req, res) => {
  res.render('daftar', { title: 'Daftar - FinChat' });
});

app.post('/daftar', async (req, res) => {
  const { name, email, password } = req.body;
  if (!name || !email || !password || password.length < 6) {
    setFlash(req, 'error', 'Nama, email, dan kata sandi minimal 6 karakter wajib diisi.');
    return res.redirect('/daftar');
  }

  try {
    const passwordHash = await bcrypt.hash(password, 10);
    const [result] = await pool.query('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', [name, email, passwordHash]);
    req.session.user = { id: result.insertId, name, email };
    setFlash(req, 'success', 'Akun berhasil dibuat. Coba catat transaksi pertamamu.');
    return res.redirect('/onboarding');
  } catch (error) {
    setFlash(req, 'error', 'Email sudah terdaftar atau data belum valid.');
    return res.redirect('/daftar');
  }
});

app.post('/keluar', (req, res) => {
  req.session.destroy(() => res.redirect('/'));
});

app.get('/onboarding', requireAuth, (req, res) => {
  res.render('onboarding', {
    title: 'Mulai - FinChat',
    exampleDate: formatDateID(new Date()),
    parsed: null
  });
});

app.post('/onboarding', requireAuth, async (req, res) => {
  try {
    const parsed = parseChatInput(req.body.message);
    await saveParsedTransaction(req.session.user.id, parsed);
    setFlash(req, 'success', `Transaksi “${parsed.description}” berhasil disimpan.`);
    return res.redirect('/dashboard');
  } catch (error) {
    setFlash(req, 'error', error.message);
    return res.redirect('/onboarding');
  }
});

app.get('/dashboard', requireAuth, async (req, res) => {
  const data = await getDashboardData(req.session.user.id, req.query.month || currentMonthInput());
  res.render('dashboard', {
    title: 'Dashboard - FinChat',
    data,
    today: toDateInputValue()
  });
});

app.post('/chat', requireAuth, async (req, res) => {
  try {
    const parsed = parseChatInput(req.body.message);
    await saveParsedTransaction(req.session.user.id, parsed);
    setFlash(req, 'success', `AI parser menyimpan ${parsed.description} sebesar ${formatRupiah(parsed.amount)}.`);
  } catch (error) {
    setFlash(req, 'error', error.message);
  }
  return res.redirect(req.body.redirectTo || '/dashboard');
});

app.get('/input-chat', requireAuth, async (req, res) => {
  const preview = req.query.text ? parseChatInput(req.query.text) : null;
  res.render('input-chat', {
    title: 'Input Chat - FinChat',
    preview,
    inputText: req.query.text || ''
  });
});

app.post('/input-chat/preview', requireAuth, (req, res) => {
  const text = encodeURIComponent(req.body.message || '');
  return res.redirect(`/input-chat?text=${text}`);
});

app.post('/input-chat/simpan', requireAuth, async (req, res) => {
  try {
    const parsed = {
      originalText: req.body.originalText || req.body.description,
      type: req.body.type,
      amount: Number(req.body.amount),
      categoryName: req.body.categoryName,
      description: req.body.description,
      transactionDate: req.body.transactionDate,
      confidence: Number(req.body.confidence || 0.9)
    };
    await saveParsedTransaction(req.session.user.id, parsed);
    setFlash(req, 'success', 'Transaksi dari chat berhasil dikonfirmasi dan disimpan.');
    return res.redirect('/riwayat');
  } catch (error) {
    setFlash(req, 'error', error.message);
    return res.redirect('/input-chat');
  }
});

app.get('/input-manual', requireAuth, async (req, res) => {
  const categories = await getCategories();
  res.render('input-manual', {
    title: 'Input Manual - FinChat',
    categories,
    today: toDateInputValue()
  });
});

app.post('/input-manual', requireAuth, async (req, res) => {
  const { type, categoryId, amount, description, transactionDate } = req.body;
  if (!type || !categoryId || !amount || !description || !transactionDate) {
    setFlash(req, 'error', 'Semua field transaksi manual wajib diisi.');
    return res.redirect('/input-manual');
  }

  await pool.query(
    `INSERT INTO transactions
    (user_id, category_id, type, amount, description, transaction_date, source_text, confidence_score)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    [req.session.user.id, categoryId, type, Number(amount), description, transactionDate, 'input manual', 1]
  );
  setFlash(req, 'success', 'Transaksi manual berhasil disimpan.');
  return res.redirect('/riwayat');
});

app.get('/riwayat', requireAuth, async (req, res) => {
  const selectedMonth = req.query.month || currentMonthInput();
  const { start, end, month } = monthRange(selectedMonth);
  const category = req.query.category || '';
  const categories = await getCategories();

  let query = `SELECT t.*, c.name AS category_name, c.icon
    FROM transactions t
    JOIN categories c ON c.id = t.category_id
    WHERE t.user_id = ? AND t.transaction_date >= ? AND t.transaction_date < ?`;
  const params = [req.session.user.id, start, end];

  if (category) {
    query += ' AND c.name = ?';
    params.push(category);
  }

  query += ' ORDER BY t.transaction_date DESC, t.id DESC';
  const [transactions] = await pool.query(query, params);

  res.render('riwayat', {
    title: 'Riwayat - FinChat',
    transactions,
    categories,
    selectedMonth: month,
    selectedCategory: category,
    monthLabel: monthLabel(month)
  });
});

app.get('/budget', requireAuth, async (req, res) => {
  const selectedMonth = req.query.month || currentMonthInput();
  const { start, end, month } = monthRange(selectedMonth);
  const expenseCategories = await getCategories('expense');

  const [budgets] = await pool.query(
    `SELECT b.*, c.name AS category_name,
      COALESCE(SUM(t.amount), 0) AS used_amount
    FROM budgets b
    JOIN categories c ON c.id = b.category_id
    LEFT JOIN transactions t ON t.user_id = b.user_id
      AND t.category_id = b.category_id
      AND t.type = 'expense'
      AND t.transaction_date >= ?
      AND t.transaction_date < ?
    WHERE b.user_id = ? AND b.month_year = ?
    GROUP BY b.id, c.name
    ORDER BY c.name ASC`,
    [start, end, req.session.user.id, month]
  );

  res.render('budget', {
    title: 'Budget - FinChat',
    budgets,
    expenseCategories,
    selectedMonth: month,
    monthLabel: monthLabel(month)
  });
});

app.post('/budget', requireAuth, async (req, res) => {
  const { categoryId, monthlyLimit, monthYear } = req.body;
  await pool.query(
    `INSERT INTO budgets (user_id, category_id, monthly_limit, month_year)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE monthly_limit = VALUES(monthly_limit)`,
    [req.session.user.id, categoryId, Number(monthlyLimit), monthYear || currentMonthInput()]
  );
  setFlash(req, 'success', 'Budget berhasil disimpan.');
  return res.redirect(`/budget?month=${monthYear || currentMonthInput()}`);
});

app.get('/profil', requireAuth, async (req, res) => {
  const [[totals]] = await pool.query(
    `SELECT COUNT(*) AS total_transactions,
      COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS income,
      COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS expense
    FROM transactions
    WHERE user_id = ?`,
    [req.session.user.id]
  );

  res.render('profil', {
    title: 'Profil - FinChat',
    totals
  });
});

app.get('/export.csv', requireAuth, async (req, res) => {
  const [rows] = await pool.query(
    `SELECT t.transaction_date, t.type, c.name AS category, t.description, t.amount, t.source_text
    FROM transactions t
    JOIN categories c ON c.id = t.category_id
    WHERE t.user_id = ?
    ORDER BY t.transaction_date DESC, t.id DESC`,
    [req.session.user.id]
  );

  const header = 'tanggal,jenis,kategori,deskripsi,nominal,source_text\n';
  const body = rows
    .map((row) =>
      [row.transaction_date, row.type, row.category, row.description, row.amount, row.source_text]
        .map((value) => `"${String(value ?? '').replace(/"/g, '""')}"`)
        .join(',')
    )
    .join('\n');

  res.setHeader('Content-Type', 'text/csv; charset=utf-8');
  res.setHeader('Content-Disposition', 'attachment; filename="finchat-transaksi.csv"');
  res.send(header + body);
});

app.get('/api/health', async (req, res) => {
  try {
    await pool.query('SELECT 1');
    res.json({ status: 'ok', service: 'finchat-dinamis', database: 'connected' });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

app.use((req, res) => {
  res.status(404).render('404', { title: 'Halaman tidak ditemukan' });
});

async function startServer() {
  await connectWithRetry();
  await runBootstrap();
  app.listen(PORT, '0.0.0.0', () => {
    console.log(`FinChat UAS berjalan di port ${PORT}`);
  });
}

startServer().catch((error) => {
  console.error('Gagal menjalankan aplikasi:', error);
  process.exit(1);
});
