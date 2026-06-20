# Entity Relationship Diagram (ERD) - Greenhouse Database

Dokumen ini berisi struktur relasional dari database `db_greenhouse`. Diagram ini digambarkan menggunakan sintaks **Mermaid**. Jika Anda membuka file ini menggunakan Markdown viewer atau platform seperti GitHub / VSCode (dengan ekstensi Mermaid), gambarnya akan di-render secara visual.

```mermaid
erDiagram
    USERS {
        int id PK
        string full_name
        string email
        string phone
        string password_hash
        enum role "customer, manager, owner, admin"
        enum status "active, offline, suspended"
        datetime created_at
    }

    PRODUCTS {
        int id PK
        string name
        string category
        string sku
        string image_path
        decimal price_per_kg
    }

    STOCK_INVENTORIES {
        int id PK
        int product_id FK
        decimal total_weight_kg
        enum grade "A, B, C"
        enum status "ADA, KOSONG, RENDAH"
        datetime last_updated
    }

    HARVEST_LOGS {
        int id PK
        int worker_id FK
        int product_id FK
        decimal yield_kg
        enum grade "A, B, C"
        date harvest_date
        enum verification_status "pending, invoiced, sold"
    }

    ORDERS {
        int id PK
        string order_number "e.g. DB-90821"
        int customer_id FK
        decimal total_amount
        string delivery_address
        enum logistic_status "Pesanan Masuk, Proses Sortir, Pengiriman, Diterima"
        datetime created_at
    }

    ORDER_ITEMS {
        int id PK
        int order_id FK
        int product_id FK
        int qty
        decimal subtotal
    }

    %% Relationships
    USERS ||--o{ HARVEST_LOGS : "mencatat_panen"
    USERS ||--o{ ORDERS : "melakukan_pesanan"
    PRODUCTS ||--o| STOCK_INVENTORIES : "memiliki_stok"
    PRODUCTS ||--o{ HARVEST_LOGS : "merupakan_hasil_dari"
    ORDERS ||--|{ ORDER_ITEMS : "berisi"
    PRODUCTS ||--o{ ORDER_ITEMS : "terdaftar_dalam"
```

## Deskripsi Singkat Tabel
1. **USERS**: Tabel sentral yang menampung multi-role akses ke sistem (Petani/Manager, Pemilik, Pembeli).
2. **PRODUCTS**: Katalog utama hasil pertanian (komoditas/sayuran).
3. **STOCK_INVENTORIES**: Tempat rekapitulasi data nyata berapa berat (`kg`) stok yang tersedia di lahan/gudang.
4. **HARVEST_LOGS**: Catatan historis atau jejak pelaporan hasil tani dari pekerja.
5. **ORDERS & ORDER_ITEMS**: Rekapitulasi transaksi pembelian oleh agen distribusi/pembeli.
