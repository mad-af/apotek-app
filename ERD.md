# Entity Relationship Diagram (ERD)

Berikut adalah diagram ERD untuk projek Apotek App berdasarkan struktur database saat ini.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    MEDICINES {
        bigint id PK
        string kode_obat "Unique"
        string nama_obat
        string satuan_obat
        double harga_obat
        integer stok_obat
        timestamp created_at
        timestamp updated_at
    }

    TRANSACTIONS {
        bigint id PK
        date transaction_date
        double total_amount
        timestamp created_at
        timestamp updated_at
    }

    TRANSACTION_ITEMS {
        bigint id PK
        bigint transaction_id FK
        bigint medicine_id FK
        integer quantity
        double price
        double total_price
        timestamp created_at
        timestamp updated_at
    }

    DISTRIBUTORS {
        bigint id PK
        string nama_distributor
        text alamat
        string no_telp
        string email
        decimal latitude
        decimal longitude
        timestamp created_at
        timestamp updated_at
    }

    TRANSACTIONS ||--|{ TRANSACTION_ITEMS : "has"
    MEDICINES ||--|{ TRANSACTION_ITEMS : "included_in"
```

## Penjelasan Relasi

1.  **Transactions - Transaction Items (1:N)**
    -   Satu `Transaction` dapat memiliki banyak `TransactionItem`.
    -   Setiap `TransactionItem` terhubung ke satu `Transaction`.
    -   Relasi ini mendefinisikan detail item apa saja yang dibeli dalam satu transaksi.

2.  **Medicines - Transaction Items (1:N)**
    -   Satu `Medicine` dapat muncul di banyak `TransactionItem` (di berbagai transaksi berbeda).
    -   Setiap `TransactionItem` mereferensikan satu jenis `Medicine`.

3.  **Users**
    -   Tabel ini digunakan untuk autentikasi (Login Admin). Saat ini belum ada relasi langsung ke transaksi (asumsi: transaksi dicatat oleh sistem/kasir umum atau user yang login, namun di database `transactions` belum ada `user_id`).

4.  **Distributors**
    -   Tabel master data untuk menyimpan informasi distributor obat. Saat ini berdiri sendiri sebagai referensi.
