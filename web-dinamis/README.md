# FinChat Lite - Web Dinamis UAS

FinChat Lite adalah web dinamis untuk UAS Administrasi Server. App ini memakai Node.js + Express + EJS + MariaDB dan dideploy dengan Docker Compose di AWS EC2.

## Fitur

- Landing page FinChat
- Login/register sederhana
- Dashboard pemasukan, pengeluaran, saldo, insight
- Input transaksi via chat dengan rule-based parser
- Input transaksi manual
- Riwayat transaksi dari MariaDB
- Filter bulan dan kategori
- Budget per kategori
- Export CSV
- Profil dan info project UAS

## Akun demo

```txt
demo@finchat.local
password123
```

## Test local

Jalankan dari folder `web-dinamis`:

```bash
docker compose up -d --build
```

Buka:

```txt
http://localhost:3000
```

Cek container:

```bash
docker ps
```

Stop:

```bash
docker compose down
```

Reset database local:

```bash
docker compose down -v
```

## Deploy EC2

Workflow GitHub Actions `deploy-web-dinamis.yml` akan:

1. Build image `web-dinamis-uas`
2. Push ke Docker Hub
3. Copy `docker-compose.yml` dan `db/init.sql` ke EC2
4. Jalankan `docker compose up -d`

URL target:

```txt
http://54.255.162.1:3000
```

Pastikan Security Group EC2 membuka port `3000`.
