-- Создание таблицы списаний
CREATE TABLE write_offs (
    id BIGSERIAL PRIMARY KEY,
    number VARCHAR(50) NOT NULL,
    date TIMESTAMP NOT NULL,
    organization VARCHAR(255) NOT NULL,
    project VARCHAR(255),
    warehouse BIGINT NOT NULL,
    status VARCHAR(50) DEFAULT 'draft',
    is_posted BOOLEAN DEFAULT false,
    comment TEXT,
    total NUMERIC(15,2) DEFAULT 0,
    overhead_costs NUMERIC(15,2) DEFAULT 0,
    created_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id BIGINT REFERENCES users(id),
    updated_at TIMESTAMP
);

-- Создание таблицы позиций списаний
CREATE TABLE write_off_positions (
    id BIGSERIAL PRIMARY KEY,
    write_off_id BIGINT REFERENCES write_offs(id) ON DELETE CASCADE,
    name VARCHAR(255),
    code VARCHAR(100),
    barcode VARCHAR(100),
    article VARCHAR(100),
    quantity NUMERIC(15,3) DEFAULT 0,
    balance NUMERIC(15,3) DEFAULT 0,
    price NUMERIC(15,2) DEFAULT 0,
    amount NUMERIC(15,2) DEFAULT 0,
    reason TEXT,
    gtd VARCHAR(100),
    rnpt VARCHAR(100),
    country VARCHAR(100),
    product_id BIGINT REFERENCES products_sklad(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Создание таблицы файлов списаний
CREATE TABLE write_off_files (
    id BIGSERIAL PRIMARY KEY,
    write_off_id BIGINT REFERENCES write_offs(id) ON DELETE CASCADE,
    filename VARCHAR(255),
    size_mb NUMERIC(10,2),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    employee VARCHAR(255),
    file_url VARCHAR(500)
);

-- Создание индексов для оптимизации
CREATE INDEX idx_write_offs_user_id ON write_offs(user_id);
CREATE INDEX idx_write_offs_warehouse ON write_offs(warehouse);
CREATE INDEX idx_write_offs_date ON write_offs(date);
CREATE INDEX idx_write_offs_status ON write_offs(status);
CREATE INDEX idx_write_off_positions_write_off_id ON write_off_positions(write_off_id);
CREATE INDEX idx_write_off_files_write_off_id ON write_off_files(write_off_id); 