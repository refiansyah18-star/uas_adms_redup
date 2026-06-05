-- ── Users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(60)  NOT NULL UNIQUE,
  full_name     VARCHAR(120) NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- Default user: username=admin / password=admin123
-- Hash dibuat via PHP password_hash() — jalankan: php db/seed_users.php
-- (tabel users dibuat di sini, user default di-insert oleh seed_users.php)

-- ── Watchlist ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS watchlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  type ENUM('Anime','Movie','Series') NOT NULL DEFAULT 'Anime',
  genre VARCHAR(80) NOT NULL,
  status ENUM('Plan to Watch','Watching','Completed','Dropped') NOT NULL DEFAULT 'Plan to Watch',
  total_episodes INT NOT NULL DEFAULT 1,
  watched_episodes INT NOT NULL DEFAULT 0,
  rating DECIMAL(3,1) NOT NULL DEFAULT 0,
  notes TEXT,
  poster_url TEXT,
  cover_url TEXT,
  added_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO watchlist
(title, type, genre, status, total_episodes, watched_episodes, rating, notes, poster_url, cover_url, added_date)
VALUES
('Attack on Titan','Anime','Action','Completed','87','87','9.2','Final arc sudah selesai. Salah satu koleksi anime utama di vault.','https://lh3.googleusercontent.com/aida-public/AB6AXuCRwX_Y0ZkSSRbkDdCvHcf-wvo_2Ct5zi0g9xviBKsZUEkkG6_A58mYdPoKeBBWep0H69MMxI1L2A-wwq5lEBjnCub6tOTxu_BuFjaB19ZCF5rFsmwkoENbWDO54HyS-SZzPvPvOq_0eozdarD1g0-daDrBphghYJZf3BPLHOaNQbSQLLTJ1vW_gpmDt8ntxSeo9aMZvQWaaZBSBpJkry8NWHpfE8byaMqpgroEMqhh3x0EIsEk92BNn8qRCu_ow3J0BXLCveYLKqRu','https://lh3.googleusercontent.com/aida-public/AB6AXuBt5exTelKRziTqDdeQ0UYnbIqDVKgQZK-NchUo2jnuH-9vGcnHSzbw_izhbyFr9xSf_HAF1U8hZZOI1DBjhh6ySDSEK7vQLJKYSvkd2_ITU-AaNNpqj1gzw9G2xChTHmPK9J4WvbcPbY6nSQ2cm1PlLNprIAIOUwnaEIi-3WQMbeC3goCQ9JJp4agrswkc1CjA_GRA7uGyhSdWOUKbm4QWikzsJeQiGuvlJ7me5g9YsChI0Q3iZRn6GoKPpdg87Hib5xArnCGXuGy7','2026-06-01'),
('Interstellar','Movie','Sci-Fi','Completed','1','1','9.4','Film sci-fi favorit untuk rewatch saat akhir pekan.','https://lh3.googleusercontent.com/aida-public/AB6AXuAlzoru0a46yI3UMrVS6CRhrYGbkQ7yDNc3knhSg3cL51IQzBNpbnbDg67MQkmEEtzxNv69oqtg7fAPYtPdFnifdIwhT0yv-PIh3E2INT8KKTmIpp9qRfDnaWAL6FqLTTTcFNMI8C2vtRSx7D3pnFYsT50RRe8bIAb85Ts2kQKzaD97bxckpQyeYkmLt2R7pjH0UvWwyFju6NlBRC-Co4Tjbm0xD9q3JoJXRG_Dr6afeC_EDvrwc-iAfLoTVFM5je91iHqqangWAY-5','https://lh3.googleusercontent.com/aida-public/AB6AXuAlzoru0a46yI3UMrVS6CRhrYGbkQ7yDNc3knhSg3cL51IQzBNpbnbDg67MQkmEEtzxNv69oqtg7fAPYtPdFnifdIwhT0yv-PIh3E2INT8KKTmIpp9qRfDnaWAL6FqLTTTcFNMI8C2vtRSx7D3pnFYsT50RRe8bIAb85Ts2kQKzaD97bxckpQyeYkmLt2R7pjH0UvWwyFju6NlBRC-Co4Tjbm0xD9q3JoJXRG_Dr6afeC_EDvrwc-iAfLoTVFM5je91iHqqangWAY-5','2026-06-02'),
('Demon Slayer','Anime','Action','Watching','26','12','8.8','Visual kuat, progress masih berjalan.','https://lh3.googleusercontent.com/aida-public/AB6AXuB-2ap6iet3-AbATZHIDEroPzx65hsl8gfcUh4rtag7wHyDVhHADQVZQ6qTDBnMljHVdVC_ANQe8sNX-nwtQwFsexbHOo7QNmYgjVB6hn3QPwpHWCnKW1CcQL4fNfRkuQkwn_Y3CCZlIecMujGSDNHTc_VYdc_xgmCS8kZsujHFvc6zP3BcnKepRJESlTsPfVfwMYIg9JHCJCaJk8tbgCcNwYDJdLFYhDz6IZ-51A4fAn6yKqpKXHTP6_7wOCOo8VnMVasOmISQhLJ-','https://lh3.googleusercontent.com/aida-public/AB6AXuBbrPmFGO8rRPTSLtswF20TbwZNSDmQlEUVKzJFbn7e_pP-rvfJrIZYcwFRjEfKH3GfHEdD-kyhIb3EQ6VwWd2wBmmKHkLEjWaOp9FueB4u8KtL4ND33rHoKN06TjxvxXhVaYf9atSMb9LRjd6_syr9SSgWtVA5VZ8TZy_XPYI5FEoyKIS_EsIQIUnmWaxmogCM4GWHZvKqQ9LMFH4_gGfo8pdX0f3A6XISRMGdKH6lfp40MDfskB3_bsZemzaaqu-_bgpkpepZleq-','2026-06-03'),
('Breaking Bad','Series','Crime','Watching','62','18','9.5','Series drama kriminal yang masuk daftar prioritas.','https://lh3.googleusercontent.com/aida-public/AB6AXuBl3HJvncD0cfxbMfJUpP0CZ_bFDBctgj4rJ_1K0qp_bBDOzaIv_st3qkHDQBMe6ROEVy4luDyVs2P9rYjc5jBi28tp3AAxwu8MsztnMzPocXhBAG7KuRCr3Uxx2OsqvwZ4tOMOMz4IRvat_LB2zLOShs0QNitkUgHIyfPllFEw4LsLK2sGyBxGrKsVZPH3131bPiBGJytF46EvgGcFyrtcw-ja8qLlG6Pv15O06KsHXKTyX0O5HPk2AitWFUBY5lCqzBFeJX9V1Azh','https://lh3.googleusercontent.com/aida-public/AB6AXuBl3HJvncD0cfxbMfJUpP0CZ_bFDBctgj4rJ_1K0qp_bBDOzaIv_st3qkHDQBMe6ROEVy4luDyVs2P9rYjc5jBi28tp3AAxwu8MsztnMzPocXhBAG7KuRCr3Uxx2OsqvwZ4tOMOMz4IRvat_LB2zLOShs0QNitkUgHIyfPllFEw4LsLK2sGyBxGrKsVZPH3131bPiBGJytF46EvgGcFyrtcw-ja8qLlG6Pv15O06KsHXKTyX0O5HPk2AitWFUBY5lCqzBFeJX9V1Azh','2026-06-04'),
('One Piece','Anime','Adventure','Watching','1200','1100','9.8','Petualangan panjang dengan progress hampir mengejar episode terbaru.','https://lh3.googleusercontent.com/aida-public/AB6AXuC7l6yt3WlY3g2vfRety1GobETIEEE2m0rF2IrAeUkZ_4BYdOvNdOGiyrT5_1EY646tQrG7OCwVgnmB-ws9Yt2RL8e1k4wG7C6y0whUqmt4nApmFcaAVMeMtRoABINpBTTMmW-K5kLstCA63YWfIVrNPhr6xYsua9E10YrARwiArzhc32pNT7pw9oWLvz7LsMmwJ5BvEuenS3tUc8AaDzXIYJh0Bx_eltrpxx0SrDOz9upWOk9wN1jUPZPh1Qt_krrVUItCobXDa-Vt','https://lh3.googleusercontent.com/aida-public/AB6AXuDxCDYmHfvPTA-wNAyF3V-mHv_BqIMCeSnatYyFxjn2U1tGcC2lz5BqOPmY9rrSKzgnqVsyZ9fWQknvLtEQlQqXHcIMkEBlaENq6CdQPenrGVysc2rH-pNY9TSdnc-Ra11DNKNCT2zTCpMP58xHrern_Vu6AcQnZRL1D10UljHSQaIwoGB2dCSKkTUPdRQzSsDB-XfUEEnNhy8FwsPtztoh2KE50qhamA9frAeHh4fcTXqs_nHEYfI4ONCfz6XiSfnVtRCt4Ju5BdLy','2026-06-04'),
('Jujutsu Kaisen','Anime','Action','Plan to Watch','47','0','0.0','Masuk antrean setelah menyelesaikan Demon Slayer.','https://lh3.googleusercontent.com/aida-public/AB6AXuBdotLFbh5xrljw-ZtQQC25nAINwW2Y6fIcBQTmhExmPkAT6jw3xkgf3kCV7uXhjrPuIo9w_DMcaK9oybv49g7f4banLUF1pToonDgiK_IhRYKPtXOBRbR07W3ELNWDTFH6IMVwqvgFEW6LTU3j6iM88hPHIM24_LWCF8mF5vGfVAadkR2kfk8o9M_eBe-lW12ENcrpxC3a5zSMxf0EB7DROt9XbMGdCJOgFoOafngbZJ-0ZJZvrfyGRoAl0wKG_TOQxrFIM_hDonkI','https://lh3.googleusercontent.com/aida-public/AB6AXuBdotLFbh5xrljw-ZtQQC25nAINwW2Y6fIcBQTmhExmPkAT6jw3xkgf3kCV7uXhjrPuIo9w_DMcaK9oybv49g7f4banLUF1pToonDgiK_IhRYKPtXOBRbR07W3ELNWDTFH6IMVwqvgFEW6LTU3j6iM88hPHIM24_LWCF8mF5vGfVAadkR2kfk8o9M_eBe-lW12ENcrpxC3a5zSMxf0EB7DROt9XbMGdCJOgFoOafngbZJ-0ZJZvrfyGRoAl0wKG_TOQxrFIM_hDonkI','2026-06-05'),
('Your Name','Movie','Romance','Completed','1','1','9.0','Film anime dengan visual dan cerita yang kuat.','https://lh3.googleusercontent.com/aida-public/AB6AXuAODpYKJ1rMRsVuNJqZ_YquWSSrhOUGzpcwLIq5rn3ElAaJnQSAInQY4atDnh5DoZ6NzoKblE6HKKAYFfUTRwXSlqwNGPyYd4I0gJkOtfnsmG1sQVAZQ9fd--A_NjNRNcpYffQnuhDWYEvV5WwW6e6HVERlb8jEQ-r62UmB82qgO9G8IuSJJNi0O4OPEv1u_uzNegrJhPUQ87HlT9EOK2Yv6EPsRCUMzYB6hlvxaq3mcUNYbitkSq4pWaJyK9kqqSk96uMPHLM1LxQn','https://lh3.googleusercontent.com/aida-public/AB6AXuAODpYKJ1rMRsVuNJqZ_YquWSSrhOUGzpcwLIq5rn3ElAaJnQSAInQY4atDnh5DoZ6NzoKblE6HKKAYFfUTRwXSlqwNGPyYd4I0gJkOtfnsmG1sQVAZQ9fd--A_NjNRNcpYffQnuhDWYEvV5WwW6e6HVERlb8jEQ-r62UmB82qgO9G8IuSJJNi0O4OPEv1u_uzNegrJhPUQ87HlT9EOK2Yv6EPsRCUMzYB6hlvxaq3mcUNYbitkSq4pWaJyK9kqqSk96uMPHLM1LxQn','2026-06-05'),
('Suzume','Movie','Fantasy','Plan to Watch','1','0','0.0','Belum ditonton. Masuk daftar film anime prioritas.','https://lh3.googleusercontent.com/aida-public/AB6AXuCakStarVzX-YxHaOZcDywakBnF0lKAlYydAzH395mehxMfnb48Z5tY0nh_6CFPQnkD99yLtVOtgXiX6-E1Z300u7gxDIWjesfoRr24_pzKJMXu5MtIP9jr2c7IiunlnSDy2d50Vf7jykZZnaK8kdbrnwDKgEk1n5jwj_MflzulXbRxhi1JXIfxmWdMIryV-zdcQpZdF3TErIVUhJ9_rNmmcQaoBchYo08A6uJDIGzARBEDa0cOfIvvgb-TDtCyhnE0lrOHkc1YbxLi','https://lh3.googleusercontent.com/aida-public/AB6AXuCakStarVzX-YxHaOZcDywakBnF0lKAlYydAzH395mehxMfnb48Z5tY0nh_6CFPQnkD99yLtVOtgXiX6-E1Z300u7gxDIWjesfoRr24_pzKJMXu5MtIP9jr2c7IiunlnSDy2d50Vf7jykZZnaK8kdbrnwDKgEk1n5jwj_MflzulXbRxhi1JXIfxmWdMIryV-zdcQpZdF3TErIVUhJ9_rNmmcQaoBchYo08A6uJDIGzARBEDa0cOfIvvgb-TDtCyhnE0lrOHkc1YbxLi','2026-06-06');
