# Paket FinChat Dynamic untuk UAS

Isi paket ini:

```txt
web-dinamis/
├── db/init.sql
├── public/style.css
├── views/*.ejs
├── Dockerfile
├── docker-compose.yml
├── package.json
└── server.js

.github/workflows/deploy-web-dinamis.yml
```

## Cara pasang ke repo `uas_redup`

1. Extract isi zip ini ke root project `uas_redup`.
2. Pastikan struktur akhirnya seperti ini:

```txt
uas_redup/
├── .github/workflows/deploy-web-statis.yml
├── .github/workflows/deploy-web-dinamis.yml
├── web-statis/
├── web-dinamis/
└── README.md
```

3. Test local dari folder `web-dinamis`:

```bash
cd web-dinamis
docker compose up -d --build
```

4. Buka:

```txt
http://localhost:3000
```

5. Akun demo:

```txt
demo@finchat.local
password123
```

## Fix UI yang sudah dimasukkan

- Tidak ada klaim OJK.
- Teks keamanan dibuat realistis: password hash dan data hanya untuk pencatatan pribadi.
- Semua nominal pakai Rupiah.
- Tanggal mengikuti format Indonesia dan data dummy dibuat konsisten mengikuti bulan berjalan.
- Landing page sudah punya demo chat, demo chat chips, dan CTA bottom section.
- Bottom nav sudah konsisten: Beranda, Riwayat, Budget, Profil.
- Social login tidak dipasang agar tidak jadi fitur mati.
- Input manual tersedia.
- Input via chat punya konfirmasi/edit sebelum simpan.
- Contoh conversational query sudah ditampilkan di landing dan halaman chat.
- Code comment bahasa Inggris tidak dipakai.

## Deploy

Push ke branch `main`, workflow `Deploy Web Dinamis UAS` akan build image dan deploy ke EC2.

Target URL:

```txt
http://54.255.162.1:3000
```
