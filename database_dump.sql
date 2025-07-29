--
-- PostgreSQL database dump
--

-- Dumped from database version 15.13 (Ubuntu 15.13-1.pgdg22.04+1)
-- Dumped by pg_dump version 17.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categories (
    id integer NOT NULL,
    category_id character varying(255),
    name character varying(255),
    icon_url character varying(255),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    product_count integer,
    group_name character varying(255),
    name_en character varying(255),
    name_ru character varying(255),
    name_uz character varying(255),
    group_name_en character varying(255),
    group_name_ru character varying(255),
    group_name_uz character varying(255)
);


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.currencies (
    id bigint NOT NULL,
    currency_id character(36) NOT NULL,
    full_name character varying(255) NOT NULL,
    currency_type character varying(10) NOT NULL,
    rate numeric(10,2) NOT NULL,
    date timestamp without time zone NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: currencies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.currencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: currencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.currencies_id_seq OWNED BY public.currencies.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: inventories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inventories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    warehouse_id bigint,
    status character varying(50) DEFAULT 'draft'::character varying,
    created_by bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    completed_at timestamp without time zone,
    notes text
);


--
-- Name: TABLE inventories; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.inventories IS 'Таблица инвентаризаций';


--
-- Name: COLUMN inventories.status; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.inventories.status IS 'Статус инвентаризации: draft, in_progress, completed, cancelled';


--
-- Name: inventories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventories_id_seq OWNED BY public.inventories.id;


--
-- Name: inventory_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inventory_files (
    id bigint NOT NULL,
    inventory_id bigint,
    filename character varying(255) NOT NULL,
    original_filename character varying(255) NOT NULL,
    file_path character varying(500) NOT NULL,
    file_size bigint,
    mime_type character varying(100),
    uploaded_by bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: TABLE inventory_files; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.inventory_files IS 'Таблица файлов инвентаризации';


--
-- Name: inventory_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventory_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventory_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventory_files_id_seq OWNED BY public.inventory_files.id;


--
-- Name: inventory_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inventory_items (
    id bigint NOT NULL,
    inventory_id bigint,
    product_id bigint,
    calculated_quantity integer DEFAULT 0 NOT NULL,
    actual_quantity integer DEFAULT 0 NOT NULL,
    notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    difference_quantity integer GENERATED ALWAYS AS ((actual_quantity - calculated_quantity)) STORED,
    excess_shortage text GENERATED ALWAYS AS (
CASE
    WHEN ((actual_quantity - calculated_quantity) > 0) THEN 'excess'::text
    WHEN ((actual_quantity - calculated_quantity) < 0) THEN 'shortage'::text
    ELSE 'normal'::text
END) STORED,
    photo character varying(500)
);


--
-- Name: TABLE inventory_items; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.inventory_items IS 'Таблица товаров инвентаризации';


--
-- Name: COLUMN inventory_items.calculated_quantity; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.inventory_items.calculated_quantity IS 'Расчетный остаток товара до инвентаризации';


--
-- Name: COLUMN inventory_items.actual_quantity; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.inventory_items.actual_quantity IS 'Фактический остаток товара после инвентаризации';


--
-- Name: inventory_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.inventory_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inventory_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.inventory_items_id_seq OWNED BY public.inventory_items.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: modifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.modifications (
    id integer NOT NULL,
    user_id integer NOT NULL,
    mod_title character varying(255) NOT NULL,
    vode_val character varying(255)
);


--
-- Name: modifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.modifications_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.modifications_id_seq OWNED BY public.modifications.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notifications (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    type character varying(50) DEFAULT 'info'::character varying,
    message text NOT NULL,
    is_read boolean DEFAULT false,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


--
-- Name: notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.notifications_id_seq OWNED BY public.notifications.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: product_balances; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_balances (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    warehouse_id bigint NOT NULL,
    quantity integer DEFAULT 0 NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: product_balances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_balances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_balances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_balances_id_seq OWNED BY public.product_balances.id;


--
-- Name: products_sklad; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.products_sklad (
    id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    category character varying(255),
    subcategory character varying(255),
    country character varying(255),
    supplier character varying(255),
    article character varying(255),
    code character varying(255),
    external_code character varying(255),
    unit character varying(50),
    weight numeric(12,3),
    volume numeric(12,3),
    vat character varying(50),
    min_stock numeric(12,3),
    stock_type character varying(50),
    packing character varying(100),
    accounting_type character varying(100),
    traceable boolean,
    marking character varying(100),
    product_type character varying(100),
    barcode_type character varying(50),
    barcode character varying(50),
    cash_register_tax character varying(100),
    cash_register_type character varying(100),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    warehouse_id bigint,
    start_count integer DEFAULT 0,
    price numeric(15,2) DEFAULT 0 NOT NULL,
    fields json
);


--
-- Name: COLUMN products_sklad.start_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.products_sklad.start_count IS 'Начальный остаток товара';


--
-- Name: COLUMN products_sklad.price; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.products_sklad.price IS 'Цена товара';


--
-- Name: warehouses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.warehouses (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    address text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: product_balances_view; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.product_balances_view AS
 SELECT pb.id,
    pb.product_id,
    ps.name AS product_name,
    ps.article AS product_article,
    pb.warehouse_id,
    w.name AS warehouse_name,
    pb.quantity,
    pb.updated_at
   FROM ((public.product_balances pb
     JOIN public.products_sklad ps ON ((pb.product_id = ps.id)))
     JOIN public.warehouses w ON ((pb.warehouse_id = w.id)))
  ORDER BY w.name, ps.name;


--
-- Name: product_fields; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_fields (
    id integer NOT NULL,
    user_id integer NOT NULL,
    field_name character varying(255) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: product_fields_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_fields_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_fields_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_fields_id_seq OWNED BY public.product_fields.id;


--
-- Name: product_images; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_images (
    id integer NOT NULL,
    product_id integer NOT NULL,
    image_url text NOT NULL,
    alt_text character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_images_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_images_id_seq OWNED BY public.product_images.id;


--
-- Name: product_operations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_operations (
    id bigint NOT NULL,
    product_id bigint NOT NULL,
    warehouse_id bigint NOT NULL,
    operation_type character varying(255) NOT NULL,
    quantity integer NOT NULL,
    reference_type character varying(255),
    reference_id bigint,
    notes text,
    created_by bigint NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: product_operations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_operations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_operations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_operations_id_seq OWNED BY public.product_operations.id;


--
-- Name: product_transfer_positions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_transfer_positions (
    id bigint NOT NULL,
    transfer_id bigint NOT NULL,
    product_id bigint NOT NULL,
    quantity integer NOT NULL,
    actual_quantity integer,
    notes text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT product_transfer_positions_quantity_check CHECK ((quantity > 0))
);


--
-- Name: TABLE product_transfer_positions; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.product_transfer_positions IS 'Позиции перемещений товаров';


--
-- Name: COLUMN product_transfer_positions.actual_quantity; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_transfer_positions.actual_quantity IS 'Фактическое количество при выполнении перемещения';


--
-- Name: product_transfer_positions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_transfer_positions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_transfer_positions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_transfer_positions_id_seq OWNED BY public.product_transfer_positions.id;


--
-- Name: product_transfers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.product_transfers (
    id bigint NOT NULL,
    from_warehouse_id bigint NOT NULL,
    to_warehouse_id bigint NOT NULL,
    transfer_date date DEFAULT CURRENT_DATE NOT NULL,
    status character varying(50) DEFAULT 'draft'::character varying NOT NULL,
    notes text,
    created_by bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    completed_at timestamp without time zone,
    completed_by bigint,
    CONSTRAINT check_different_warehouses CHECK ((from_warehouse_id <> to_warehouse_id)),
    CONSTRAINT check_valid_status CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'confirmed'::character varying, 'completed'::character varying, 'cancelled'::character varying])::text[])))
);


--
-- Name: TABLE product_transfers; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.product_transfers IS 'Перемещения товаров между складами';


--
-- Name: COLUMN product_transfers.status; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.product_transfers.status IS 'Статус: draft - черновик, confirmed - подтвержден, completed - выполнен, cancelled - отменен';


--
-- Name: product_transfers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.product_transfers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: product_transfers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.product_transfers_id_seq OWNED BY public.product_transfers.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.products (
    id integer NOT NULL,
    glink character varying NOT NULL,
    title text NOT NULL,
    title_ru text NOT NULL,
    title_en text NOT NULL,
    title_uz text NOT NULL,
    titles jsonb NOT NULL,
    description text NOT NULL,
    characters text NOT NULL,
    vendore text NOT NULL,
    main_photo_url text NOT NULL,
    thumb text NOT NULL,
    video_url text NOT NULL,
    photos jsonb NOT NULL,
    out_photo text NOT NULL,
    price integer NOT NULL,
    price_usd numeric(10,2) NOT NULL,
    count integer NOT NULL,
    min_order_count integer NOT NULL,
    country text NOT NULL,
    sell_count integer NOT NULL,
    opt_price integer NOT NULL,
    discount integer NOT NULL,
    opt_discount integer NOT NULL,
    currency text NOT NULL,
    price_with_nds integer NOT NULL,
    nds_percent integer NOT NULL,
    measure_unit text NOT NULL,
    brand text NOT NULL,
    active boolean NOT NULL,
    moderated boolean NOT NULL,
    owner_rating integer NOT NULL,
    order_count integer NOT NULL,
    company_name text NOT NULL,
    user_id character varying NOT NULL,
    category_id character varying NOT NULL,
    subcategory_id character varying NOT NULL,
    views integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.products_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: products_sklad_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.products_sklad_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: products_sklad_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.products_sklad_id_seq OWNED BY public.products_sklad.id;


--
-- Name: receipt_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.receipt_files (
    id bigint NOT NULL,
    receipt_id bigint,
    filename character varying(255),
    size_mb numeric(10,2),
    uploaded_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    employee character varying(255),
    file_url character varying(500)
);


--
-- Name: receipt_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.receipt_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: receipt_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.receipt_files_id_seq OWNED BY public.receipt_files.id;


--
-- Name: receipt_positions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.receipt_positions (
    id bigint NOT NULL,
    receipt_id bigint,
    name character varying(255),
    code character varying(100),
    barcode character varying(100),
    article character varying(100),
    quantity integer DEFAULT 0,
    balance numeric(15,3) DEFAULT 0,
    price numeric(15,2) DEFAULT 0,
    amount numeric(15,2) DEFAULT 0,
    reason text,
    gtd character varying(100),
    rnpt character varying(100),
    country character varying(100),
    product_id bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: receipt_positions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.receipt_positions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: receipt_positions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.receipt_positions_id_seq OWNED BY public.receipt_positions.id;


--
-- Name: receipt_tasks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.receipt_tasks (
    id bigint NOT NULL,
    receipt_id bigint,
    task text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: receipt_tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.receipt_tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: receipt_tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.receipt_tasks_id_seq OWNED BY public.receipt_tasks.id;


--
-- Name: receipts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.receipts (
    id bigint NOT NULL,
    number character varying(50) NOT NULL,
    date timestamp without time zone NOT NULL,
    organization character varying(255) NOT NULL,
    project character varying(255),
    warehouse bigint NOT NULL,
    status character varying(50) DEFAULT 'draft'::character varying,
    is_posted boolean DEFAULT false,
    comment text,
    total numeric(15,2) DEFAULT 0,
    overhead_costs numeric(15,2) DEFAULT 0,
    created_by character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id bigint,
    updated_at timestamp without time zone
);


--
-- Name: receipts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.receipts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: receipts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.receipts_id_seq OWNED BY public.receipts.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: subcategories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subcategories (
    id integer NOT NULL,
    subcategory_id character varying(255),
    name character varying(255),
    category_id character varying(255),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    product_count integer,
    name_en character varying(255),
    name_ru character varying(255),
    name_uz character varying(255)
);


--
-- Name: user_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_categories (
    id integer NOT NULL,
    user_id integer NOT NULL,
    category_id character varying(255) NOT NULL,
    name character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: user_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_categories_id_seq OWNED BY public.user_categories.id;


--
-- Name: user_subcategories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_subcategories (
    id integer NOT NULL,
    user_id integer NOT NULL,
    subcategory_id character varying(255) NOT NULL,
    name character varying(255),
    category_id character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: user_subcategories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_subcategories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_subcategories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_subcategories_id_seq OWNED BY public.user_subcategories.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id integer NOT NULL,
    user_id character varying NOT NULL,
    glink text NOT NULL,
    role integer DEFAULT 0,
    acc_type text NOT NULL,
    status_subscription integer NOT NULL,
    first_name text NOT NULL,
    last_name text NOT NULL,
    user_name text NOT NULL,
    "position" text NOT NULL,
    email text NOT NULL,
    phone_number text NOT NULL,
    verified_email boolean NOT NULL,
    phone_ok boolean NOT NULL,
    password text NOT NULL,
    fcm_token text NOT NULL,
    fcm_token_android text NOT NULL,
    timezone text NOT NULL,
    last_logged_in text NOT NULL,
    is_online boolean NOT NULL,
    language text NOT NULL,
    messages_language text NOT NULL,
    country text NOT NULL,
    city text NOT NULL,
    avatar_url text NOT NULL,
    banned boolean NOT NULL,
    currency text DEFAULT 'USD'::text NOT NULL,
    balance numeric(15,10) NOT NULL,
    ref_balance numeric(15,10) NOT NULL,
    demo_balance integer NOT NULL,
    bonus_balance numeric(15,10) NOT NULL,
    inn text NOT NULL,
    comp_pinfl bigint NOT NULL,
    comp_state boolean NOT NULL,
    company_type text NOT NULL,
    company_name text NOT NULL,
    company_description text NOT NULL,
    company_rating integer NOT NULL,
    com_address text NOT NULL,
    com_leader text NOT NULL,
    comp_logo_url text NOT NULL,
    comp_phone text NOT NULL,
    comp_mail text NOT NULL,
    comp_website_url text NOT NULL,
    company_link text NOT NULL,
    company_statuses text NOT NULL,
    comp_verified integer NOT NULL,
    comp_tariff integer NOT NULL,
    deal_seen boolean NOT NULL,
    notification_email boolean NOT NULL,
    notification_email_deal boolean NOT NULL,
    notification_email_system boolean NOT NULL,
    notification_email_chat boolean NOT NULL,
    notification_email_subscription boolean NOT NULL,
    notification_sms_chat boolean NOT NULL,
    notification_sms_custom boolean NOT NULL,
    notification_sms_system boolean NOT NULL,
    is_active boolean DEFAULT true,
    catch text NOT NULL,
    reg_date bigint NOT NULL,
    moderated boolean NOT NULL,
    gen_key text NOT NULL,
    referer text NOT NULL,
    invite_link text NOT NULL,
    deleted boolean NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    product_fields_visibility json,
    cats_type character varying(10) DEFAULT 'system'::character varying NOT NULL,
    CONSTRAINT users_cats_type_check CHECK (((cats_type)::text = ANY ((ARRAY['system'::character varying, 'user'::character varying])::text[])))
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: warehouses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.warehouses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: warehouses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.warehouses_id_seq OWNED BY public.warehouses.id;


--
-- Name: write_off_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.write_off_files (
    id bigint NOT NULL,
    write_off_id bigint,
    filename character varying(255),
    size_mb numeric(10,2),
    uploaded_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    employee character varying(255),
    file_url character varying(500)
);


--
-- Name: write_off_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.write_off_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: write_off_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.write_off_files_id_seq OWNED BY public.write_off_files.id;


--
-- Name: write_off_positions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.write_off_positions (
    id bigint NOT NULL,
    write_off_id bigint,
    name character varying(255),
    code character varying(100),
    barcode character varying(100),
    article character varying(100),
    quantity numeric(15,3) DEFAULT 0,
    balance numeric(15,3) DEFAULT 0,
    price numeric(15,2) DEFAULT 0,
    amount numeric(15,2) DEFAULT 0,
    reason text,
    gtd character varying(100),
    rnpt character varying(100),
    country character varying(100),
    product_id bigint,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


--
-- Name: write_off_positions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.write_off_positions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: write_off_positions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.write_off_positions_id_seq OWNED BY public.write_off_positions.id;


--
-- Name: write_offs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.write_offs (
    id bigint NOT NULL,
    number character varying(50) NOT NULL,
    date timestamp without time zone NOT NULL,
    organization character varying(255),
    project character varying(255),
    warehouse bigint NOT NULL,
    status character varying(50) DEFAULT 'draft'::character varying,
    is_posted boolean DEFAULT false,
    comment text,
    total numeric(15,2) DEFAULT 0,
    overhead_costs numeric(15,2) DEFAULT 0,
    created_by character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    user_id bigint,
    updated_at timestamp without time zone
);


--
-- Name: write_offs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.write_offs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: write_offs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.write_offs_id_seq OWNED BY public.write_offs.id;


--
-- Name: currencies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.currencies ALTER COLUMN id SET DEFAULT nextval('public.currencies_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: inventories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventories ALTER COLUMN id SET DEFAULT nextval('public.inventories_id_seq'::regclass);


--
-- Name: inventory_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_files ALTER COLUMN id SET DEFAULT nextval('public.inventory_files_id_seq'::regclass);


--
-- Name: inventory_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_items ALTER COLUMN id SET DEFAULT nextval('public.inventory_items_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: modifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modifications ALTER COLUMN id SET DEFAULT nextval('public.modifications_id_seq'::regclass);


--
-- Name: notifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications ALTER COLUMN id SET DEFAULT nextval('public.notifications_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: product_balances id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances ALTER COLUMN id SET DEFAULT nextval('public.product_balances_id_seq'::regclass);


--
-- Name: product_fields id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_fields ALTER COLUMN id SET DEFAULT nextval('public.product_fields_id_seq'::regclass);


--
-- Name: product_images id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_images ALTER COLUMN id SET DEFAULT nextval('public.product_images_id_seq'::regclass);


--
-- Name: product_operations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations ALTER COLUMN id SET DEFAULT nextval('public.product_operations_id_seq'::regclass);


--
-- Name: product_transfer_positions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfer_positions ALTER COLUMN id SET DEFAULT nextval('public.product_transfer_positions_id_seq'::regclass);


--
-- Name: product_transfers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers ALTER COLUMN id SET DEFAULT nextval('public.product_transfers_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: products_sklad id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products_sklad ALTER COLUMN id SET DEFAULT nextval('public.products_sklad_id_seq'::regclass);


--
-- Name: receipt_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_files ALTER COLUMN id SET DEFAULT nextval('public.receipt_files_id_seq'::regclass);


--
-- Name: receipt_positions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_positions ALTER COLUMN id SET DEFAULT nextval('public.receipt_positions_id_seq'::regclass);


--
-- Name: receipt_tasks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_tasks ALTER COLUMN id SET DEFAULT nextval('public.receipt_tasks_id_seq'::regclass);


--
-- Name: receipts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipts ALTER COLUMN id SET DEFAULT nextval('public.receipts_id_seq'::regclass);


--
-- Name: user_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_categories ALTER COLUMN id SET DEFAULT nextval('public.user_categories_id_seq'::regclass);


--
-- Name: user_subcategories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subcategories ALTER COLUMN id SET DEFAULT nextval('public.user_subcategories_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: warehouses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.warehouses ALTER COLUMN id SET DEFAULT nextval('public.warehouses_id_seq'::regclass);


--
-- Name: write_off_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_files ALTER COLUMN id SET DEFAULT nextval('public.write_off_files_id_seq'::regclass);


--
-- Name: write_off_positions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_positions ALTER COLUMN id SET DEFAULT nextval('public.write_off_positions_id_seq'::regclass);


--
-- Name: write_offs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_offs ALTER COLUMN id SET DEFAULT nextval('public.write_offs_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categories (id, category_id, name, icon_url, created_at, updated_at, product_count, group_name, name_en, name_ru, name_uz, group_name_en, group_name_ru, group_name_uz) FROM stdin;
11	selskoe-khozyaystvo	Сельское хозяйство	12.png	2024-10-17 12:34:35	\N	4	Сельское хозяйство	Agriculture	Сельское хозяйство	Qishloq xo'jaligi	Agriculture	Сельское хозяйство	Qishloq xo'jaligi
12	stroitelstvo-i-nedvijimost	Строительство и недвижимость	11.png	2024-10-17 12:34:35	\N	655	Строительство и недвижимость	Construction and real estate	Строительство и недвижимость	Qurilish va ko'chmas mulk	Construction and real estate	Строительство и недвижимость	Qurilish va ko'chmas mulk
14	khimikati	Химикаты и сырье	4.png	2024-10-17 12:34:35	2025-01-21 12:19:35	56	Промышленные товары и сырье	Chemicals and raw materials	Химикаты и сырье	Kimyoviy moddalar va xom ashyo	Industrial goods and raw materials	Промышленные товары и сырье	Sanoat tovarlari va xom ashyolari
19	minerali-i-metallurgiya	Минералы и металлургия	4.png	2024-10-17 12:34:35	\N	2	Промышленные товары и сырье	Minerals and Metallurgy	Минералы и металлургия	Minerallar va metallurgiya	Industrial goods and raw materials	Промышленные товары и сырье	Sanoat tovarlari va xom ashyolari
22	krasota-i-lichnaya-gigiena	Красота и личная гигиена	6.png	2024-10-17 12:34:35	\N	148	Здоровье и красота	Beauty and personal hygiene	Красота и личная гигиена	Go'zallik va shaxsiy gigiena	Health and beauty	Здоровье и красота	Salomatlik va go'zallik
26	servisnoe-oborudovanie-i-prinadlejnosti	Сервисное оборудование и принадлежности	1.png	2024-10-17 12:34:35	\N	2	Технологии и электроника	Service equipment and accessories	Сервисное оборудование и принадлежности	Xizmat uskunalari va aksessuarlari	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
28	elektrotekhnicheskoe-oborudovanie-i-materiali	Электротехническое оборудование и материалы	1.png	2024-10-17 12:34:35	\N	47	Технологии и электроника	Electrical equipment and materials	Электротехническое оборудование и материалы	Elektr jihozlari va materiallari	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
29	bitovaya-elektronika	Бытовая электроника	1.png	2024-10-17 12:34:35	\N	1119	Технологии и электроника	Consumer electronics	Бытовая электроника	Maishiy elektronika	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
30	bitovaya-tekhnika	Бытовая техника	1.png	2024-10-17 12:34:35	\N	2324	Технологии и электроника	Household appliances	Бытовая техника	Maishiy texnika	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
35	bezopasnost-i-zatshita	Безопасность и защита	14.png	2024-10-17 12:34:35	\N	4	Безопасность и защита	Safety and Security	Безопасность и защита	Xavfsizlik va xavfsizlik	Safety and Security	Безопасность и защита	Xavfsizlik va xavfsizlik
39	instrumenti-i-oborudovaniya	Инструменты и оборудование	4.png	2024-10-17 12:34:35	2025-01-21 11:20:45	30	Промышленные товары и сырье	Tools and equipment	Инструменты и оборудование	Asboblar va jihozlar	Industrial goods and raw materials	Промышленные товары и сырье	Sanoat tovarlari va xom ashyolari
47	energiya	Энергия	13.png	2024-10-17 12:34:35	\N	2	Энергия	Energy	Энергия	Energiya	Energy	Энергия	Energiya
55	avtomobili-i-aksessuary	Автомобили и аксессуары	9.png	2025-01-20 04:05:06	2025-01-21 02:24:59	548	Авто и транспорт	Cars and Accessories	Автомобили и аксессуары	Avtomobillar va aksessuarlar	Cars and transport	Авто и транспорт	Avtomobillar va transport
56	mobilnaya-elektronika	Мобильная электроника и связь	1.png	2025-01-20 04:05:06	2025-01-21 12:09:00	610	Технологии и электроника	Mobile electronics and communications	Мобильная электроника и связь	Mobil elektronika va aloqa	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
58	umnaya-bytovaya-elektronika	Умная бытовая электроника	1.png	2025-01-20 04:05:06	\N	1092	Технологии и электроника	Smart home electronics	Умная бытовая электроника	Aqlli uy elektronikasi	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
60	podarki,-festivali-i-khobbi	Подарки, фестивали и хобби	8.png	2025-01-20 04:05:06	\N	177	Подарки и развлечения	Gifts, festivals and hobbies	Подарки, фестивали и хобби	Sovg'alar, bayramlar va sevimli mashg'ulotlar	Gifts and entertainment	Подарки и развлечения	Sovg'alar va o'yin-kulgilar
61	apparatnoe-obespechenie	Аппаратное обеспечение	1.png	2025-01-20 04:05:06	\N	3514	Технологии и электроника	Hardware	Аппаратное обеспечение	Uskuna	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
62	eda-i-napitki	Еда и напитки	10.png	2025-01-20 04:05:06	\N	1600	Еда и напитки	Food and Drinks	Еда и напитки	Oziq-ovqat va ichimliklar	Food and Drinks	Еда и напитки	Oziq-ovqat va ichimliklar
63	sport-i-otdykh-na-prirode	Спорт и отдых. Развлечения	7.png	2025-01-20 04:05:06	2025-01-21 12:39:58	457	Спорт и активный отдых	Sports and recreation. Entertainment	Спорт и отдых. Развлечения	Sport va dam olish. O'yin-kulgi	Sports and active recreation	Спорт и активный отдых	Sport va faol dam olish
64	shkolnye-i-ofisnye-prinadlezhnosti	Школьные и офисные принадлежности	15.png	2025-01-20 04:05:06	\N	697	Школьные и офисные принадлежности	School and office supplies	Школьные и офисные принадлежности	Maktab va ofis jihozlari	School and office supplies	Школьные и офисные принадлежности	Maktab va ofis jihozlari
66	zdravookhranenie	Здравоохранение	6.png	2025-01-20 04:05:06	\N	687	Здоровье и красота	Healthcare	Здравоохранение	Sog'liqni saqlash	Health and beauty	Здоровье и красота	Salomatlik va go'zallik
67	modnye-aksessuary-i-obuv	Модные аксессуары и обувь	3.png	2025-01-20 04:05:06	\N	361	Мода и аксессуары	Fashion accessories and shoes	Модные аксессуары и обувь	Moda aksessuarlari va poyafzallari	Fashion and accessories	Мода и аксессуары	Moda va aksessuarlar
69	mashiny-i-oborudovanie	Машины и оборудование	4.png	2025-01-20 04:05:06	2025-01-21 01:08:15	5527	Промышленные товары и сырье	Machines and equipment	Машины и оборудование	Mashina va uskunalar	Industrial goods and raw materials	Промышленные товары и сырье	Sanoat tovarlari va xom ashyolari
70	pechat-i-upakovka	Печать и упаковка	5.png	2025-01-20 04:05:06	\N	572	Печать и упаковка	Printing and packaging	Печать и упаковка	Chop etish va qadoqlash	Printing and packaging	Печать и упаковка	Chop etish va qadoqlash
71	mebel-i-domashniy-dekor	Мебель, декор и освещение	2.png	2025-01-20 04:05:06	2025-01-21 11:57:34	1364	Дом и интерьер	Furniture, decor and lighting	Мебель, декор и освещение	Mebel, dekoratsiya va yoritish	Home and Interior	Дом и интерьер	Uy va ichki makon
72	domashnie-i-domashnie-zhivotnye	Дом и сад	2.png	2025-01-20 04:05:06	2025-01-21 01:54:41	431	Дом и интерьер	Home and garden	Дом и сад	Uy va bog'	Home and Interior	Дом и интерьер	Uy va ichki makon
73	elektronnye-komponenty	Электронные компоненты и материалы	1.png	2025-01-20 04:05:06	2025-01-21 12:23:05	14304	Технологии и электроника	Electronic components and materials	Электронные компоненты и материалы	Elektron komponentlar va materiallar	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
74	modnaya-odezhda-i-tkani	Одежда и ткани	3.png	2025-01-20 04:05:06	\N	195	Мода и аксессуары	Clothes and fabrics	Одежда и ткани	Kiyim va matolar	Fashion and accessories	Мода и аксессуары	Moda va aksessuarlar
75	promyshlennye-postavki	Промышленные поставки и Окружающая среда	4.png	2025-01-20 04:05:06	2025-01-21 12:10:11	5941	Промышленные товары и сырье	Industrial Supplies and Environment	Промышленные поставки и Окружающая среда	Sanoat ta'minoti va atrof-muhit	Industrial goods and raw materials	Промышленные товары и сырье	Sanoat tovarlari va xom ashyolari
79	75eff93b-9170-4360-97fd-5ec3f5964d76	Сетевое оборудование	\N	2025-04-02 14:28:54	\N	1	Технологии и электроника	Network equipment	Сетевое оборудование	Tarmoq uskunalari	Technology and Electronics	Технологии и электроника	Texnologiya va elektronika
80	344b927d-ca9d-4e98-9338-19ad5f16d828	Складская спецтехника	\N	2025-04-03 13:12:36	2025-04-03 13:14:03	12	Спецтехника	Special equipment	Складская спецтехника	Maxsus jihozlar	Special equipment	Спецтехника	Maxsus jihozlar
\.


--
-- Data for Name: currencies; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.currencies (id, currency_id, full_name, currency_type, rate, date, created_at, updated_at) FROM stdin;
9	ff37fe01-edb2-4b06-b53e-ebb201c7e94c	Chinese Yuan	CNY	7.18	2025-07-27 19:00:00	2024-10-08 21:10:19	2025-07-28 19:00:00
10	31657335-a8a5-47ea-a96e-309329b95695	Hong Kong Dollar	HKD	7.85	2025-07-27 19:00:00	2024-10-08 21:11:06	2025-07-28 19:00:00
11	26fb9c92-56fa-42c0-8913-eb11974bfcbb	New Zealand Dollar	NZD	1.68	2025-07-27 19:00:00	2024-10-08 21:11:39	2025-07-28 19:00:00
12	6c21b621-eea7-44a3-81fa-463d934eac99	Russian Ruble	RUB	81.35	2025-07-27 19:00:00	2024-10-08 21:12:08	2025-07-28 19:00:00
1	d6c5a34e-541e-4235-b65e-f7df14809447	United States Dollar	USD	1.00	2025-07-27 19:00:00	2024-10-08 21:03:47	2025-07-28 19:00:00
2	b98d5d88-7d40-4c78-889c-214df5fc049f	Uzbekistani Som	UZS	12589.04	2025-07-27 19:00:00	2024-10-08 21:04:25	2025-07-28 19:00:00
3	409b1dbd-4f61-46d8-b405-80133582be30	Euro	EUR	0.86	2025-07-27 19:00:00	2024-10-08 21:04:58	2025-07-28 19:00:00
4	46b42a1e-0c1b-4019-b8ea-2ecab2caf707	Japanese Yen	JPY	148.52	2025-07-27 19:00:00	2024-10-08 21:05:58	2025-07-28 19:00:00
5	96c6324e-c742-4d56-8da8-7d6512122519	British Pound Sterling	GBP	0.75	2025-07-27 19:00:00	2024-10-08 21:08:01	2025-07-28 19:00:01
6	5d48fb65-4b31-4d07-b9d0-1ff2b47b4c6a	Australian Dollar	AUD	1.53	2025-07-27 19:00:00	2024-10-08 21:08:37	2025-07-28 19:00:01
7	7509c100-8994-4576-be61-cb29f17a47b1	Canadian Dollar	CAD	1.37	2025-07-27 19:00:00	2024-10-08 21:09:14	2025-07-28 19:00:01
8	f94c2b89-cd4a-417b-b26b-0806d0ce1ee6	Swiss Franc	CHF	0.80	2025-07-27 19:00:00	2024-10-08 21:09:42	2025-07-28 19:00:01
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: inventories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inventories (id, name, description, warehouse_id, status, created_by, created_at, updated_at, completed_at, notes) FROM stdin;
14	Инвентаризация 2025	\N	7	draft	45	2025-07-16 13:50:20	2025-07-16 13:50:20	\N	\N
16	Тестовая инвентаризация с расхождениями	Автоматический тест функции создания операций	1	completed	52	2025-07-23 08:31:00	2025-07-23 08:31:00	\N	Тест автоматических операций
17	23.07.2025	\N	13	completed	56	2025-07-23 09:28:53	2025-07-23 09:28:53	\N	\N
18	111	\N	13	completed	56	2025-07-23 09:30:10	2025-07-23 09:30:10	\N	\N
19	3	\N	13	completed	56	2025-07-23 09:31:45	2025-07-23 09:31:45	\N	\N
21	11111	-	13	completed	56	2025-07-23 09:52:35	2025-07-23 09:52:35	\N	\N
23	1112	-	9	completed	52	2025-07-23 10:26:35	2025-07-23 10:26:35	\N	\N
24	11	--	1	completed	47	2025-07-24 11:47:02	2025-07-24 11:47:02	\N	\N
29	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара ID: 92	1	completed	52	2025-07-26 07:12:42	2025-07-26 07:12:42	2025-07-26 07:12:42	\N
30	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара ID: 73	1	completed	52	2025-07-26 07:17:15	2025-07-26 07:17:15	2025-07-26 07:17:15	\N
31	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для массового импорта товаров	1	completed	52	2025-07-26 07:17:17	2025-07-26 07:17:17	2025-07-26 07:17:17	\N
25	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара ID: 92	1	completed	52	2025-07-26 07:10:10	2025-07-26 07:10:10	2025-07-26 07:10:10	\N
26	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара ID: 92	1	completed	52	2025-07-26 07:10:44	2025-07-26 07:10:44	2025-07-26 07:10:44	\N
27	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для массового импорта товаров	1	completed	52	2025-07-26 07:11:33	2025-07-26 07:11:33	2025-07-26 07:11:33	\N
28	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для массового импорта товаров	1	completed	52	2025-07-26 07:12:00	2025-07-26 07:12:00	2025-07-26 07:12:00	\N
32	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: Iphone 15 pro max	1	completed	52	2025-07-26 07:18:58	2025-07-26 07:18:58	2025-07-26 07:18:58	\N
33	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товаров: Iphone 15 pro max	1	completed	52	2025-07-26 07:19:01	2025-07-26 07:19:01	2025-07-26 07:19:01	\N
34	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: Провод медный ВВГнг 3x2.5	9	completed	52	2025-07-26 07:29:27	2025-07-26 07:29:27	2025-07-26 07:29:27	\N
35	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: тест	9	completed	52	2025-07-26 07:34:31	2025-07-26 07:34:31	2025-07-26 07:34:31	\N
37	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товаров: Краска акриловая белая, Удобрение азотное NPK	9	completed	52	2025-07-26 07:44:26	2025-07-26 07:44:26	2025-07-26 07:44:26	\N
38	Тестовая инвентаризация с избытком	Автотест создания оприходования	1	completed	52	2025-07-26 07:52:07	2025-07-26 07:52:07	\N	\N
39	Тестовая инвентаризация с избытком	Автотест создания оприходования	1	completed	52	2025-07-26 07:52:40	2025-07-26 07:52:40	\N	\N
40	Тестовая инвентаризация с избытком	Автотест создания оприходования	1	completed	52	2025-07-26 07:54:03	2025-07-26 07:54:03	\N	\N
41	Тестовая инвентаризация с избытком	Автотест создания оприходования	1	completed	52	2025-07-26 07:55:37	2025-07-26 07:55:37	\N	\N
44	тест авто списаний и оприходований	тест авто списаний и оприходований	9	completed	52	2025-07-26 08:04:44	2025-07-26 08:04:44	\N	\N
45	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: Test	3	completed	47	2025-07-26 08:37:27	2025-07-26 08:37:27	2025-07-26 08:37:27	\N
46	test	test	1	completed	47	2025-07-26 08:52:22	2025-07-26 08:52:22	\N	\N
47	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: test 2	3	completed	47	2025-07-26 08:56:38	2025-07-26 08:56:38	2025-07-26 08:56:38	\N
48	Добавление начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: 1335	1	completed	47	2025-07-26 11:42:41	2025-07-26 11:42:41	2025-07-26 11:42:41	\N
49	Изменение начальных остатков от 26.07.2025	Автоматическая инвентаризация для товара: 1335	1	completed	47	2025-07-26 11:43:52	2025-07-26 11:43:52	2025-07-26 11:43:52	\N
50	lkjlk	\N	1	completed	47	2025-07-26 11:48:00	2025-07-26 11:48:00	\N	\N
51	111	\N	1	completed	47	2025-07-26 11:49:31	2025-07-26 11:49:31	\N	\N
53	Массовая инвентаризация от 28.07.2025	Автоматическая инвентаризация для товаров: Бетон М300 готовый, Клей ПВА универсальный	9	completed	52	2025-07-28 03:51:23	2025-07-28 03:51:23	\N	\N
\.


--
-- Data for Name: inventory_files; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inventory_files (id, inventory_id, filename, original_filename, file_path, file_size, mime_type, uploaded_by, created_at, updated_at) FROM stdin;
9	14	Снимокпп.png	Снимокпп.png	https://api.b2bstorage.ru/storage/inventory-files/6877ae172a8ef_1752673815.png	520898	\N	45	2025-07-16 13:50:20	2025-07-16 13:50:20
10	21	c7bbe0ae5e097f7ad050.png	c7bbe0ae5e097f7ad050.png	https://api.b2bstorage.ru/storage/inventory-files/6880b0d5e5ad1_1753264341.png	2909	\N	56	2025-07-23 09:52:35	2025-07-23 09:52:35
\.


--
-- Data for Name: inventory_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inventory_items (id, inventory_id, product_id, calculated_quantity, actual_quantity, notes, created_at, updated_at, photo) FROM stdin;
40	16	94	75	75	Без расхождений	2025-07-23 08:31:02	2025-07-23 08:31:02	\N
41	17	102	150	151	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
42	17	103	25	24	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
43	17	104	80	80	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
44	17	105	500	500	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
45	17	106	2000	2000	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
46	17	107	150	150	\N	2025-07-23 09:28:53	2025-07-23 09:28:53	\N
47	18	102	150	152	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
48	18	103	25	23	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
49	18	104	80	80	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
50	18	105	500	500	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
51	18	106	2000	2000	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
52	18	107	150	150	\N	2025-07-23 09:30:10	2025-07-23 09:30:10	\N
53	19	104	80	70	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
54	19	105	500	550	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
55	19	106	2000	2000	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
56	19	107	150	150	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
57	19	102	154	154	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
58	19	103	21	21	\N	2025-07-23 09:31:45	2025-07-23 09:31:45	\N
59	21	104	80	82	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
60	21	105	500	490	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
61	21	106	2000	2000	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
62	21	107	150	150	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
63	21	102	154	154	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
64	21	103	21	21	\N	2025-07-23 09:52:35	2025-07-23 09:52:35	\N
72	23	73	10	11	123	2025-07-23 10:26:35	2025-07-23 10:26:35	http://127.0.0.1:8000/storage/inventory-items-photos/inventory_item_6880b8819e600_1753266305.png
73	23	74	5	5	\N	2025-07-23 10:26:36	2025-07-23 10:26:36	\N
74	23	75	10	9	456	2025-07-23 10:26:37	2025-07-23 10:26:37	http://127.0.0.1:8000/storage/inventory-items-photos/inventory_item_6880b8881531d_1753266312.png
76	23	94	2000	2000	\N	2025-07-23 10:26:39	2025-07-23 10:26:39	\N
79	24	119	150	140	123	2025-07-24 11:47:02	2025-07-24 11:47:02	https://api.b2bstorage.ru/storage/inventory-items-photos/inventory_item_68821d1c3c084_1753357596.webp
80	24	120	25	27	опраорп	2025-07-24 11:47:02	2025-07-24 11:47:02	https://api.b2bstorage.ru/storage/inventory-items-photos/inventory_item_68821d1eea96f_1753357598.webp
81	24	121	80	80	\N	2025-07-24 11:47:02	2025-07-24 11:47:02	\N
82	24	122	500	500	\N	2025-07-24 11:47:02	2025-07-24 11:47:02	\N
83	24	123	2000	2000	\N	2025-07-24 11:47:02	2025-07-24 11:47:02	\N
84	24	124	150	150	\N	2025-07-24 11:47:02	2025-07-24 11:47:02	\N
89	28	43	0	53	Создание начального остатка 53	2025-07-26 07:12:02	2025-07-26 07:12:02	\N
90	28	45	0	15	Создание начального остатка 15	2025-07-26 07:12:03	2025-07-26 07:12:03	\N
92	30	73	0	100	Создание начального остатка 100	2025-07-26 07:17:16	2025-07-26 07:17:16	\N
93	31	73	0	50	Создание начального остатка 50	2025-07-26 07:17:18	2025-07-26 07:17:18	\N
94	32	73	0	150	Создание начального остатка 150	2025-07-26 07:18:59	2025-07-26 07:18:59	\N
95	33	73	0	75	Создание начального остатка 75	2025-07-26 07:19:02	2025-07-26 07:19:02	\N
96	34	94	0	2100	Изменение начального остатка с 2000 на 2100	2025-07-26 07:29:28	2025-07-26 07:29:28	\N
97	35	126	10	15	Изменение начального остатка с 10 на 15	2025-07-26 07:34:32	2025-07-26 07:34:32	\N
103	37	133	0	80	Создание начального остатка 80	2025-07-26 07:44:27	2025-07-26 07:44:27	\N
104	37	134	0	500	Создание начального остатка 500	2025-07-26 07:44:28	2025-07-26 07:44:28	\N
105	38	43	10	15	Тест избытка	2025-07-26 07:52:08	2025-07-26 07:52:08	\N
106	39	43	10	15	Тест избытка	2025-07-26 07:52:41	2025-07-26 07:52:41	\N
107	40	43	10	15	Тест избытка	2025-07-26 07:54:04	2025-07-26 07:54:04	\N
108	41	43	10	15	Тест избытка	2025-07-26 07:55:38	2025-07-26 07:55:38	\N
125	44	133	80	70	\N	2025-07-26 08:04:45	2025-07-26 08:04:45	\N
126	44	134	500	510	\N	2025-07-26 08:04:46	2025-07-26 08:04:46	\N
127	44	73	10	10	\N	2025-07-26 08:04:47	2025-07-26 08:04:47	\N
128	44	74	5	5	\N	2025-07-26 08:04:48	2025-07-26 08:04:48	\N
129	44	75	10	10	\N	2025-07-26 08:04:49	2025-07-26 08:04:49	\N
131	44	94	2100	2100	\N	2025-07-26 08:04:51	2025-07-26 08:04:51	\N
132	44	126	15	15	\N	2025-07-26 08:04:51	2025-07-26 08:04:51	\N
133	45	135	0	1000	Изменение начального остатка с 0 на 1000	2025-07-26 08:37:27	2025-07-26 08:37:27	\N
134	46	119	150	160	123	2025-07-26 08:52:22	2025-07-26 08:52:22	https://api.b2bstorage.ru/storage/inventory-items-photos/inventory_item_6884973a36b18_1753519930.png
135	46	120	25	20	456	2025-07-26 08:52:22	2025-07-26 08:52:22	https://api.b2bstorage.ru/storage/inventory-items-photos/inventory_item_6884973b25d88_1753519931.png
136	46	121	80	80	\N	2025-07-26 08:52:22	2025-07-26 08:52:22	\N
137	46	122	500	500	\N	2025-07-26 08:52:22	2025-07-26 08:52:22	\N
138	46	123	2000	2000	\N	2025-07-26 08:52:22	2025-07-26 08:52:22	\N
139	46	124	150	150	\N	2025-07-26 08:52:22	2025-07-26 08:52:22	\N
145	50	119	150	151	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
146	50	120	25	20	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
147	50	121	80	80	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
148	50	122	500	500	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
149	50	123	2000	2000	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
150	50	124	150	150	\N	2025-07-26 11:48:00	2025-07-26 11:48:00	\N
153	51	119	150	160	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
154	51	120	25	30	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
155	51	121	80	85	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
156	51	122	500	400	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
157	51	123	2000	1900	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
158	51	124	150	50	\N	2025-07-26 11:49:31	2025-07-26 11:49:31	\N
161	53	151	0	25	Создание начального остатка 25	2025-07-28 03:51:26	2025-07-28 03:51:26	\N
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_07_03_084605_create_personal_access_tokens_table	2
5	2025_07_02_103104_create_personal_access_tokens_table	3
6	2025_07_02_112732_create_sessions_table	4
7	2025_07_02_102909_create_users_table	1
8	2025_07_03_131830_create_personal_access_tokens_table	5
\.


--
-- Data for Name: modifications; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.modifications (id, user_id, mod_title, vode_val) FROM stdin;
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.notifications (id, user_id, type, message, is_read, created_at, updated_at) FROM stdin;
3	52	recommendation	Общие рекомендации по управлению запасами:\n\nКонечно, вот несколько практичных рекомендаций по улучшению управления складскими запасами для небольшого бизнеса:\n\n1. **Проведите анализ спроса.** Изучите историю продаж, чтобы понять паттерны и сезонность спроса на товары. Это поможет оптимизировать уровень запасов и избежать излишков или нехватки.\n\n2. **Внедрите систему учета товаров.** Используйте программное обеспечение для учета складских запасов, которое позволит отслеживать поступление и отгрузку товаров, контролировать остатки и автоматизировать процессы.\n\n3. **Оптимизируйте заказы поставщикам.** Разработайте стратегию заказов, опираясь на данные о спросе и сроках поставки. Старайтесь заказывать товары в оптимальных количествах, чтобы избежать излишков и сократить затраты на хранение.\n\n4. **Проводите регулярный инвентаризацию.** Планируйте периодические инвентаризации, чтобы сверить фактические остатки с данными в учетной системе. Это поможет избежать потерь и ошибок в учете.\n\n5. **Обучите персонал.** Обеспечьте сотрудников знаниями и навыками по управлению запасами. Обучите их правилам складского учета, техникам инвентаризации и оптимизации процессов складского хозяйства.\n\nНадеюсь, эти рекомендации помогут вам оптимизировать управление складскими запасами в вашем бизнесе. Если у вас есть дополнительные вопросы или нужна дополнительная помощь, не стесняйтесь обращаться!	t	2025-07-26 09:53:54	2025-07-26 10:35:38
4	52	recommendation	Общие рекомендации по управлению запасами:\n\n1. **Анализ и оптимизация запасов:**\n   - Провести анализ продаж по каждому товару за последние 3-6 месяцев.\n   - Определить среднемесячный спрос на каждый товар и рассчитать оптимальный уровень запасов.\n   - Пересмотреть заказные точки для товаров с низким остатком и увеличить заказы с учетом спроса.\n\n2. **Управление поставками:**\n   - Связаться с поставщиками для уточнения сроков поставок и возможности ускорения поставок для товаров с высоким спросом.\n   - Рассмотреть возможность заключения долгосрочных договоров с надежными поставщиками для обеспечения стабильности поставок.\n\n3. **Оптимизация складских процессов:**\n   - Внедрить систему складского учета для автоматизации процесса учета товаров и отслеживания остатков.\n   - Определить оптимальное распределение товаров по складам для сокращения времени на поиск и комплектацию заказов.\n\n4. **Мониторинг и контроль:**\n   - Вести ежедневный мониторинг остатков и продаж для оперативной корректировки заказов и запасов.\n   - Установить систему уведомлений о низких остатках для своевременного заказа новой партии товаров.\n\n5. **Обучение персонала:**\n   - Провести обучение сотрудников по методам управления запасами, оптимизации заказов и использованию складской системы учета.\n   - Повысить ответственность и осознанность сотрудников по вопросам управления запасами и контроля остатков.\n\n6. **Анализ эффективности:**\n   - Регулярно проводить анализ эффективности управления запасами по ключевым показателям: уровень сервиса, оборачиваемость запасов, стоимость хранения.\n   - Принимать меры по улучшению процессов на основе полученных данных и показателей.\n\n7. **Сотрудничество с отделом продаж:**\n   - Установить тесное взаимодействие между отделом продаж и управлением запасами для обмена информацией о прогнозах спроса и планировании заказов.\n   - Проводить совместные совещания для выявления изменений в спросе и оперативного реагирования на них.	t	2025-07-26 10:16:52	2025-07-26 10:35:38
5	52	recommendation	Общие рекомендации по управлению запасами:\n\nКонечно, вот несколько практичных рекомендаций для улучшения управления складскими запасами:\n\n1. **Анализ товаров с низким остатком**:\n   - Провести более детальный анализ товаров с низким остатком (≤10) для понимания причин недостатка запасов.\n   - Оценить спрос на эти товары и их оборачиваемость, чтобы определить оптимальные уровни запасов.\n\n2. **Оптимизация запасов**:\n   - Пересмотреть стратегию управления запасами и определить оптимальные уровни безопасности, заказа и оборачиваемости для каждого товара.\n   - Использовать методы прогнозирования спроса для точного планирования запасов.\n\n3. **Управление складами**:\n   - Оптимизировать процессы на складах, чтобы уменьшить время на обработку заказов и перераспределение товаров между складами.\n   - Реализовать систему мониторинга и контроля остатков на складах для быстрого реагирования на изменения спроса.\n\n4. **Улучшение коммуникации**:\n   - Установить эффективную систему коммуникации между отделами продаж, закупок и склада для более точного прогнозирования спроса и планирования запасов.\n\n5. **Анализ документооборота**:\n   - Изучить документы, связанные с приходом и отгрузкой товаров, чтобы выявить возможные задержки или ошибки в процессе управления запасами.\n   - Оптимизировать процессы документооборота для ускорения операций и снижения риска ошибок.\n\n6. **Обучение персонала**:\n   - Обучить сотрудников, ответственных за управление складскими запасами, современным методам управления запасами и эффективному использованию программного обеспечения.\n\n7. **Постоянное обновление стратегии**:\n   - Регулярно проводить аудит системы управления запасами и вносить коррективы в стратегию в зависимости от изменений в бизнес-процессах и рыночных условиях.\n\nЭти рекомендации помогут оптимизировать процессы управления складскими запасами и повысить эффективность работы вашего бизнеса.	t	2025-07-26 10:27:14	2025-07-26 10:35:38
6	52	recommendation	Общие рекомендации по управлению запасами:\n\nКонечно, вот пять практичных рекомендаций по улучшению управления складскими запасами для вашего бизнеса:\n\n1. Анализ товаров с низким остатком:\n   - Провести детальный анализ товаров с остатком ≤10 для определения частоты спроса и возможной причины низкого остатка.\n   - Приоритизировать пополнение запасов по этим товарам, учитывая спрос и сроки поставки.\n\n2. Оптимизация запасов:\n   - Рассмотреть возможность оптимизации запасов на складах с учетом частоты оборота товаров и их стоимости.\n   - Установить минимальные и максимальные уровни запасов для каждого товара, основываясь на анализе спроса.\n\n3. Организация поставок и отгрузок:\n   - Оптимизировать график поставок и отгрузок для уменьшения задержек и избыточных запасов.\n   - Автоматизировать процесс учета поставок и отгрузок для более точного прогнозирования остатков.\n\n4. Мониторинг остатков на разных складах:\n   - Регулярно контролировать остатки товаров на разных складах и проводить перераспределение запасов для балансировки.\n   - Осуществлять ежедневное согласование между складами для избежания излишков или дефицита товаров.\n\n5. Анализ документов и прогнозирование спроса:\n   - Изучить документы за неделю для выявления паттернов спроса и сезонных колебаний.\n   - Применить методы прогнозирования спроса (например, методы временных рядов) для более точного планирования закупок.\n\nЭти рекомендации помогут оптимизировать управление запасами, повысить эффективность складского хозяйства и снизить издержки вашего бизнеса.	t	2025-07-26 10:47:12	2025-07-26 10:58:29
7	52	recommendation	Общие рекомендации по управлению запасами:\n\nКонечно, вот несколько практичных рекомендаций по улучшению управления складскими запасами для данного бизнеса:\n\n1. Проведите анализ товаров с низким остатком:\n   - Определите, какие товары чаще всего имеют низкий остаток.\n   - Проверьте, есть ли возможность увеличения заказов на эти товары для улучшения запасов.\n\n2. Оптимизируйте процесс заказов:\n   - Проследите, как часто и в каком объеме заказываются товары.\n   - Рассмотрите возможность сокращения числа заказов, объединения заказов или установления минимального порога заказа.\n\n3. Внедрите систему прогнозирования спроса:\n   - Используйте данные о продажах для прогнозирования спроса на товары.\n   - Учитывайте сезонные колебания и тенденции спроса при планировании запасов.\n\n4. Организуйте мониторинг складских запасов:\n   - Ведите учет товаров на каждом складе и контролируйте их движение.\n   - Регулярно делайте инвентаризацию для исключения потерь и избыточных запасов.\n\n5. Обучите персонал правильной работе с запасами:\n   - Проведите тренинги для сотрудников по правилам учета и управления складскими запасами.\n   - Объясните важность оптимальных запасов для эффективной работы бизнеса.\n\n6. Анализируйте данные о движении товаров:\n   - Используйте информацию о продажах и поставках для выявления популярных и медленно продаваемых товаров.\n   - Основываясь на анализе, корректируйте стратегию управления запасами.\n\n7. Свяжитесь с поставщиками:\n   - Обсудите возможность оптимизации поставок и сроков доставки товаров.\n   - Рассмотрите варианты сотрудничества, которые позволят снизить издержки на хранение и улучшить обслуживание заказов.\n\nПосле внедрения этих рекомендаций, рекомендуется регулярно оценивать их эффективность и вносить коррективы в стратегию управления складскими запасами в зависимости от изменения условий и потребностей бизнеса.	t	2025-07-26 10:52:43	2025-07-26 10:58:29
8	52	recommendation	Общие рекомендации по управлению запасами:\n\nКонечно, вот несколько практичных рекомендаций по улучшению управления складскими запасами для вашего бизнеса:\n\n1. **Анализ остатков товаров с низким количеством (≤10)**:\n   - **Действие**: Провести дополнительный анализ спроса на товары с низким остатком и определить возможность увеличения заказов или пересмотра ассортимента.\n   - **Цель**: Избежать ситуаций дефицита и потери клиентов из-за отсутствия товаров.\n\n2. **Оптимизация заказов**:\n   - **Действие**: Использовать данные о количестве документов за неделю для прогнозирования потребностей и оптимизации заказов.\n   - **Цель**: Сократить издержки на хранение излишков и уменьшить риски нехватки товаров.\n\n3. **Управление запасами по складам**:\n   - **Действие**: Распределить запасы товаров между складами исходя из данных о спросе и доступности на каждом из них.\n   - **Цель**: Снизить время доставки и оптимизировать использование складских ресурсов.\n\n4. **Мониторинг товарооборота**:\n   - **Действие**: Регулярно отслеживать скорость оборота товаров и идентифицировать медленно продаваемые позиции.\n   - **Цель**: Определить товары, требующие дополнительной рекламы или акций для увеличения спроса.\n\n5. **Внедрение системы ABC-анализа**:\n   - **Действие**: Классифицировать товары по степени значимости и продвигать стратегии управления запасами в зависимости от класса (A, B, C).\n   - **Цель**: Сосредоточить усилия на наиболее важных продуктах и оптимизировать управление остальными.\n\n6. **Внедрение системы "Just-In-Time"**:\n   - **Действие**: Пересмотреть процесс поставок с целью минимизации запасов и синхронизации поставок с реальным спросом.\n   - **Цель**: Снизить издержки на хранение и улучшить общую эффективность цепочки поставок.\n\n7. **Обучение персонала по управлению запасами**:\n   - **Действие**: Провести тренинги и семинары для сотрудников, ответственных за управление складскими запасами.\n   - **Цель**: Обеспечить глубокое понимание процессов и методик управления	t	2025-07-26 11:07:23	2025-07-26 11:07:54
10	52	recommendation	### Общие рекомендации по управлению запасами:\n\n1. **Анализ и оптимизация запасов:**\n   - Проведите анализ популярности товаров и их оборачиваемости. Уделите внимание товарам с нулевым остатком, возможно, они либо не пользуются спросом, либо есть проблемы с поставками.\n   - Рассмотрите возможность снижения запасов товаров с низким оборотом и пересмотрите ассортимент, чтобы увеличить оборачиваемость товаров.\n\n2. **Управление поставками:**\n   - Свяжитесь с поставщиками товаров, которые имеют нулевой остаток, чтобы узнать причины недостатка товара на складе.\n   - Оптимизируйте процесс закупок, учитывая динамику продаж и прогноз спроса.\n\n3. **Оптимизация складских процессов:**\n   - Определите складские зоны для удобства и эффективности хранения товаров.\n   - Внедрите систему маркировки товаров и отслеживания их перемещений на складе для сокращения времени поиска и улучшения инвентаризации.\n\n4. **Мониторинг и контроль:**\n   - Установите систему учета товаров для контроля остатков на складе.\n   - Внедрите систему уведомлений о низких запасах товаров для оперативного реагирования на ситуации дефицита.\n\n5. **Обучение персонала:**\n   - Обучите сотрудников склада работе с системой учета и отслеживания товаров.\n   - Проведите обучение по оптимизации складских процессов и управлению запасами.\n\n6. **Анализ эффективности:**\n   - Регулярно анализируйте данные по остаткам и продажам для выявления эффективности управления запасами.\n   - Оцените оборачиваемость товаров и рентабельность складских операций.\n\n7. **Сотрудничество с отделом продаж:**\n   - Поддерживайте постоянную коммуникацию с отделом продаж для прогнозирования спроса и планирования закупок.\n   - Анализируйте данные о продажах для корректировки запасов и оптимизации ассортимента.\n\nЭти рекомендации помогут вам эффективно управлять запасами, оптимизировать процессы и повысить эффективность работы склада.	t	2025-07-28 03:57:09	2025-07-28 06:43:40
12	52	recommendation	Конечно, взглянем на предоставленные данные и сформулируем рекомендации по управлению запасами:\n\n1. **Анализ и оптимизация запасов:**\n   - Проведите анализ остатков всех товаров на складах. Особое внимание уделите товарам с низким остатком (1-10) и товарам без указанного склада.\n   - Рассмотрите возможность реализации товаров, у которых остаток равен 0, если они неактуальны для вашего бизнеса.\n\n2. **Управление поставками:**\n   - Оцените частоту и объемы поставок для каждого товара. Установите оптимальные интервалы поставок и минимальные заказы.\n   - Взаимодействуйте с поставщиками для обеспечения своевременных и эффективных поставок.\n\n3. **Оптимизация складских процессов:**\n   - Разработайте эффективную систему размещения товаров на складе для удобства отслеживания и быстрого доступа.\n   - Обеспечьте правильную маркировку и категоризацию товаров для уменьшения времени на поиск и комплектацию заказов.\n\n4. **Мониторинг и контроль:**\n   - Внедрите систему учета запасов и отслеживайте изменения остатков в реальном времени.\n   - Проводите регулярные инвентаризации для выявления расхождений и предотвращения утерь товаров.\n\n5. **Обучение персонала:**\n   - Проведите обучение сотрудников по правилам управления запасами, использованию складского оборудования и программного обеспечения.\n   - Стимулируйте персонал к ответственному отношению к управлению запасами и выполнению складских процессов.\n\n6. **Анализ эффективности:**\n   - Постоянно анализируйте данные о движении товаров, остатках и затратах на хранение.\n   - Оценивайте эффективность запасов, исключая излишки и недостатки, а также оптимизируйте процессы для уменьшения издержек.\n\n7. **Сотрудничество с отделом продаж:**\n   - Установите эффективную коммуникацию между отделом продаж и складом для прогнозирования спроса и планирования запасов.\n   - Обменивайтесь информацией о популярных товарах, акциях и изменениях в спросе для оптимизации запасов и удовлетворения пот	f	2025-07-28 06:45:46	2025-07-28 06:45:46
11	47	recommendation	### 1. Анализ и оптимизация запасов\n- Проведите анализ товаров с нулевым остатком и определите причины их недостатка. Возможно, стоит пересмотреть ассортимент или увеличить заказы на эти позиции.\n- Разработайте стратегию управления запасами для каждого товара, учитывая их оборачиваемость и спрос.\n\n### 2. Управление поставками\n- Оцените частоту и объемы поставок для каждого товара с учетом их оборачиваемости и сезонности спроса.\n- Разработайте план закупок, учитывая минимальные и максимальные уровни запасов для каждого товара.\n\n### 3. Оптимизация складских процессов\n- Оптимизируйте размещение товаров на складе, учитывая их оборачиваемость и частоту запросов.\n- Внедрите систему маркировки и мест хранения для ускорения отбора товаров.\n\n### 4. Мониторинг и контроль\n- Установите систему мониторинга запасов и автоматические уведомления о низких уровнях запасов.\n- Проводите регулярные инвентаризации для контроля точности учета товаров.\n\n### 5. Обучение персонала\n- Проведите обучение сотрудников по эффективному управлению запасами, работе с программным обеспечением склада и правилам хранения товаров.\n\n### 6. Анализ эффективности\n- Регулярно анализируйте показатели оборачиваемости товаров, уровни запасов, сроки хранения и себестоимость хранения запасов.\n- Оцените эффективность внедренных изменений и корректируйте стратегию управления запасами при необходимости.\n\n### 7. Сотрудничество с отделом продаж\n- Установите регулярный обмен информацией о прогнозах спроса и изменениях в ассортименте с отделом продаж.\n- Согласуйте планы поставок и расходов с отделом продаж для минимизации рисков недостатка товаров или излишков.\n\nПоддерживайте постоянное взаимодействие между отделами, чтобы обеспечить эффективное управление запасами и достижение общих целей компании.	t	2025-07-28 06:36:04	2025-07-28 12:24:32
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
1	App\\Models\\User	33	auth_token	fbf41a6a2d84d59b35bac9acd98b659c81b04f11b36bfa02e852f92babd064bc	["*"]	\N	\N	2025-07-03 13:19:44	2025-07-03 13:19:44
2	App\\Models\\User	33	auth_token	16a2c0ab6dab089a07720c1248c1fc53448622aeeed8fefe5c264b84cae1e3e4	["*"]	\N	\N	2025-07-03 13:19:58	2025-07-03 13:19:58
3	App\\Models\\User	34	auth_token	3603486078ada48b2181d450868939005a1e14d325e7e1a10eaf38a27b789957	["*"]	\N	\N	2025-07-03 13:20:11	2025-07-03 13:20:11
4	App\\Models\\User	35	auth_token	0a4fa9581b2a7d57cd616df38a9d596aeff4a4cdf038532e0c9db0ddb5e7ddd0	["*"]	\N	\N	2025-07-03 13:24:40	2025-07-03 13:24:40
5	App\\Models\\User	35	auth_token	c04cfcfd81acc6be70534490b7cb6f0327ac6f3f8ea6f4807cefc72b02b4096e	["*"]	\N	\N	2025-07-03 13:25:06	2025-07-03 13:25:06
6	App\\Models\\User	35	auth_token	e03da3e01f99dac7c5a2242aa6223d62d9d51589eb4bede5a51b09ce4650e1cc	["*"]	\N	\N	2025-07-03 13:25:27	2025-07-03 13:25:27
7	App\\Models\\User	36	auth_token	bb8dc0cf03c4a2caf5b03d49e3504c66cb2b0aa14eec6f5ae73895c9a4a1bd4e	["*"]	\N	\N	2025-07-03 13:27:15	2025-07-03 13:27:15
8	App\\Models\\User	35	auth_token	21e356e379387fc141f1e62377cf3da77a07d2db555512caf637989b225b4e01	["*"]	\N	\N	2025-07-03 13:29:20	2025-07-03 13:29:20
9	App\\Models\\User	35	auth_token	b644226b31ddef04014f604b5120c4a000fdd5c2d43b1c1958b35f8580ffb1e9	["*"]	\N	\N	2025-07-03 13:32:15	2025-07-03 13:32:15
10	App\\Models\\User	38	auth_token	27b9c59ec38614318fd074c2dee3ce8346b43dc7027e022d2430782301c1d798	["*"]	\N	\N	2025-07-03 13:32:53	2025-07-03 13:32:53
11	App\\Models\\User	39	auth_token	3fabaa960ac3e7dc45036ef022cf63661750b9bf423f114f390cfe97d0ab7c88	["*"]	\N	\N	2025-07-03 13:33:36	2025-07-03 13:33:36
12	App\\Models\\User	39	auth_token	5c710910a6bc55905dd7b4f2c213fa2023e0c095791a753b980419c55ab39169	["*"]	\N	\N	2025-07-03 13:33:54	2025-07-03 13:33:54
13	App\\Models\\User	40	auth_token	f66e8f8f415b61276f46520c152449a8b3e2e0efc43164d3473b7a50adfc0485	["*"]	\N	\N	2025-07-03 13:34:03	2025-07-03 13:34:03
14	App\\Models\\User	39	auth_token	2aec4b24571caf46a313c5d7a605308a4b56a40620ee483a2aae0b4b8919e37b	["*"]	\N	\N	2025-07-03 13:41:28	2025-07-03 13:41:28
15	App\\Models\\User	41	auth_token	3ea54c61948ff950540157c958f28ae2eb0fda39c1b93194f7630ca372edd0b3	["*"]	\N	\N	2025-07-03 13:59:02	2025-07-03 13:59:02
16	App\\Models\\User	41	auth_token	122425c64cec0a50b394f21e2bcb47d39b81462be85e8b44f72cd4f8f62663fa	["*"]	\N	\N	2025-07-03 13:59:19	2025-07-03 13:59:19
17	App\\Models\\User	42	auth_token	9985f588910ce00787aea4b9e49bbe189153485cee77c5a9b56217d6b7a37061	["*"]	\N	\N	2025-07-03 14:22:16	2025-07-03 14:22:16
18	App\\Models\\User	42	auth_token	5d1d23a7279d542d1c553bf7641884dc718f5ef2e9b4fb6a3d9f100741561bc4	["*"]	\N	\N	2025-07-03 14:22:40	2025-07-03 14:22:40
19	App\\Models\\User	42	auth_token	47eea7b1f0a56963e24cedd1fa614b408bb75773491153510511d8c113fce615	["*"]	\N	\N	2025-07-03 14:28:40	2025-07-03 14:28:40
20	App\\Models\\User	39	auth_token	355b4846030c1d886f6917f9eef1f7b8da8468385a25e3fba29ddb4bdc12d810	["*"]	\N	\N	2025-07-03 14:38:42	2025-07-03 14:38:42
21	App\\Models\\User	39	auth_token	f425e944cb66dfc1f27e5817111031b06ce5eaf6430b0f607afd0bf2f3201ca4	["*"]	\N	\N	2025-07-03 14:39:02	2025-07-03 14:39:02
22	App\\Models\\User	43	auth_token	f69f4c443c328b851ebe0507e428eb38a998d083ad3c9c5b917783419fae3a65	["*"]	\N	\N	2025-07-03 14:39:08	2025-07-03 14:39:08
23	App\\Models\\User	44	auth_token	f128c6940af5c400ded5c1cc618bec317f01733907d114c0e758983db0bc4771	["*"]	\N	\N	2025-07-03 14:39:53	2025-07-03 14:39:53
24	App\\Models\\User	44	auth_token	89e6739b033ea62a107da4e6585c11dbf48fc65923bdb9819152cd22694d5789	["*"]	\N	\N	2025-07-03 14:40:05	2025-07-03 14:40:05
25	App\\Models\\User	39	auth_token	541a99bc1512dca93bb85b6e44f3f2ab319bd2de4b01a7d3e5608e2a93d52213	["*"]	\N	\N	2025-07-03 14:41:16	2025-07-03 14:41:16
26	App\\Models\\User	39	auth_token	de0f79574c9701cab2deb47c1771398e1e8f0953eb81c9a6d970ec2b897a2dac	["*"]	\N	\N	2025-07-03 14:41:59	2025-07-03 14:41:59
28	App\\Models\\User	39	auth_token	033fec220c07b83f4595f3a170ee662669d3e987dbb77ff8b42fe3ab64e9b557	["*"]	\N	\N	2025-07-04 04:13:33	2025-07-04 04:13:33
29	App\\Models\\User	39	auth_token	5857956c4645176e6aab592b29fe4fa3276f9743cc313c9489da0e33f6bdacb9	["*"]	\N	\N	2025-07-04 04:23:39	2025-07-04 04:23:39
30	App\\Models\\User	39	auth_token	503f824bdbe1e394e602378e3b4b4e6a0d4f9a070eaf2760d25bde286b6d64ac	["*"]	\N	\N	2025-07-04 04:28:03	2025-07-04 04:28:03
31	App\\Models\\User	39	auth_token	09f01d72a94902d9249fc777a3b987cdf8f39a18e533eb6b8c524ff12e5e71ae	["*"]	\N	\N	2025-07-04 07:25:19	2025-07-04 07:25:19
32	App\\Models\\User	39	auth_token	a0eb2d7b96d265137b190e6d29b6f9dcb447c8aeee178ac9d09944206590c5a8	["*"]	\N	\N	2025-07-04 07:37:30	2025-07-04 07:37:30
41	App\\Models\\User	47	auth_token	53d19e887a773392316b874bc6c55466b522f86170601395bf76512ceee8c027	["*"]	2025-07-07 12:29:20	\N	2025-07-07 12:27:01	2025-07-07 12:29:20
36	App\\Models\\User	39	auth_token	3df2ad8fde01714c51a2fc039e19348d28be2510028b23d509e245c4de585119	["*"]	2025-07-07 10:52:45	\N	2025-07-04 11:29:35	2025-07-07 10:52:45
34	App\\Models\\User	39	test-token	60b8144d55be13f0d5e9d7fcf402923d9bcdf957c03d817d2cb6fef50f0b1002	["*"]	2025-07-04 10:40:25	\N	2025-07-04 10:39:48	2025-07-04 10:40:25
33	App\\Models\\User	39	auth_token	cb122f336b97526cf43ca163e4d603ecf6a2d1f70f32d0dbb3c24de99b215edb	["*"]	2025-07-07 10:52:59	\N	2025-07-04 07:38:59	2025-07-07 10:52:59
37	App\\Models\\User	39	auth_token	c670d3c010579982316653d457b8fe568c646cc25756137d3b044b31417f5169	["*"]	2025-07-10 04:58:23	\N	2025-07-07 02:47:46	2025-07-10 04:58:23
39	App\\Models\\User	39	auth_token	d11b6648df781ef913891d308d1ce7361eb418dc5b61ce47c45f6e8bc310c44e	["*"]	2025-07-07 12:22:50	\N	2025-07-07 12:22:48	2025-07-07 12:22:50
35	App\\Models\\User	39	frontend-token	2942310eb24fbfe07cbaaec157b1270b8ab32511826254517b17b7e39770d19e	["*"]	2025-07-04 11:03:42	\N	2025-07-04 10:40:57	2025-07-04 11:03:42
42	App\\Models\\User	39	auth_token	b164ff65f7e6b75c8941dcce64940254bff1da4655d4ebcf0fe1466a35813d70	["*"]	2025-07-07 12:29:54	\N	2025-07-07 12:29:54	2025-07-07 12:29:54
38	App\\Models\\User	46	auth_token	2a7cda7a093976f6fc202696e06168112c1b49f9473fe6adb5674c1bdf00e7aa	["*"]	2025-07-07 09:30:08	\N	2025-07-07 09:29:40	2025-07-07 09:30:08
40	App\\Models\\User	39	auth_token	9a0d3d03a809b0aed9c225d241818c8cfe80adec6dc28f8773dbe77f8d1552a7	["*"]	2025-07-07 12:26:48	\N	2025-07-07 12:26:26	2025-07-07 12:26:48
45	App\\Models\\User	49	auth_token	db3e3be2c3a6f47014abe9d27dda98351342ab66f857762f45e026bedb84c9ef	["*"]	2025-07-07 15:31:38	\N	2025-07-07 15:31:35	2025-07-07 15:31:38
44	App\\Models\\User	48	auth_token	93ea2ef7292ac54323c0b5d6ee1cf33c46227b3485bd17827527ee8112e22269	["*"]	2025-07-07 15:48:09	\N	2025-07-07 15:27:58	2025-07-07 15:48:09
43	App\\Models\\User	47	auth_token	744be3d5535a4869a06a481df9e1b206ac531eb574fec0d34184f837b689751d	["*"]	2025-07-09 06:46:24	\N	2025-07-07 12:30:18	2025-07-09 06:46:24
52	App\\Models\\User	47	auth_token	373e7cae50f35be5f06b813c07d37bb2bfaf494dd763db403a0ed9c9f5658911	["*"]	2025-07-14 11:17:56	\N	2025-07-14 11:17:27	2025-07-14 11:17:56
72	App\\Models\\User	53	auth_token	dab24bfa0c2a2f6ff49ec691e457af00af141684b327ab62cafc8b3a919f206a	["*"]	2025-07-18 10:49:52	\N	2025-07-18 10:49:36	2025-07-18 10:49:52
62	App\\Models\\User	53	auth_token	1d506f302e69d1a85a02da91ce2f4e99bfe55d0789506228d0de4bfa5caa25e4	["*"]	2025-07-17 09:13:36	\N	2025-07-17 06:48:18	2025-07-17 09:13:36
69	App\\Models\\User	47	auth_token	c7c5122b360146e7ebea290a33dd27184771f6ff774e7e5bc53b8ecdc153125a	["*"]	2025-07-18 06:15:47	\N	2025-07-18 06:15:37	2025-07-18 06:15:47
53	App\\Models\\User	39	auth_token	e327814131c0a1cf5dd87d8d1c0f1fdc5047fb23ff2eb55c27fb31beba0a3f9f	["*"]	2025-07-14 11:36:31	\N	2025-07-14 11:23:23	2025-07-14 11:36:31
47	App\\Models\\User	50	auth_token	86a9d92714ffac74666e1ccc341dd66ad8b899724006aad8ff4286faec7d3675	["*"]	2025-07-08 11:00:15	\N	2025-07-08 10:58:43	2025-07-08 11:00:15
56	App\\Models\\User	51	auth_token	9c0cf35098ce731ab46ffcd24466e17db5748e10fba4292da617204b80eba35d	["*"]	2025-07-15 08:46:10	\N	2025-07-15 08:45:54	2025-07-15 08:46:10
46	App\\Models\\User	39	auth_token	b06a50a2e794728268c8efebf73d784a8e9d76c6b29292f663defec43b00d84e	["*"]	2025-07-14 11:22:28	\N	2025-07-08 06:18:17	2025-07-14 11:22:28
59	App\\Models\\User	39	auth_token	f2ff8927a6a8b8f2cfe258989056e932bff8a37a554d6411b7a14dd9ef4bef94	["*"]	2025-07-16 05:37:38	\N	2025-07-16 05:30:26	2025-07-16 05:37:38
63	App\\Models\\User	47	auth_token	efde4f89b03ee4838d12c867489bd2355cfc7145ec43d584ff8e014ab1a74404	["*"]	2025-07-17 09:21:15	\N	2025-07-17 08:11:47	2025-07-17 09:21:15
54	App\\Models\\User	47	auth_token	2bacb1daf1aa0c25b8d509f10f4b2a277d582fafb0453d349748cb70f9333072	["*"]	2025-07-16 05:12:51	\N	2025-07-14 11:36:51	2025-07-16 05:12:51
58	App\\Models\\User	47	auth_token	8ae29b4756842d753a9a0a0a37cdf7fd985b8fc4adb9556a0ed165491892e00f	["*"]	2025-07-16 05:29:56	\N	2025-07-16 05:29:50	2025-07-16 05:29:56
50	App\\Models\\User	47	auth_token	3a2f8344d1aa5298c9023c79fb22dc60474a1f95d444a900f76ee5fe5d29ef89	["*"]	2025-07-14 11:14:18	\N	2025-07-09 11:20:48	2025-07-14 11:14:18
27	App\\Models\\User	45	auth_token	8e9e3dae35038ecfc24416652fa25199846544572f5f1c87d78e6f2e3ba41bbb	["*"]	2025-07-17 13:31:59	\N	2025-07-03 16:24:29	2025-07-17 13:31:59
73	App\\Models\\User	39	auth_token	4b2c2b15a3c29ce3ad87d255a7713f7b216a4e32e18e4b7fb8afbab9358a473e	["*"]	2025-07-18 10:57:28	\N	2025-07-18 10:57:26	2025-07-18 10:57:28
57	App\\Models\\User	47	auth_token	c0f2600dee60b98791982b617530137deab790a37b432492f3241fe596c025a0	["*"]	2025-07-16 06:26:21	\N	2025-07-16 05:16:45	2025-07-16 06:26:21
48	App\\Models\\User	50	auth_token	a53aeccbed9ca752b0fd5e1a2d52a1df6a452badfcfbfb8ec77c9a9051d86ca4	["*"]	2025-07-10 04:41:44	\N	2025-07-08 11:46:13	2025-07-10 04:41:44
74	App\\Models\\User	54	auth_token	ac6fc25fb3cdc8975a097ce5fdbe9863942c9ea56099e74c4d4a6aee41f4e897	["*"]	\N	\N	2025-07-18 11:23:43	2025-07-18 11:23:43
49	App\\Models\\User	47	auth_token	e55c6cdb718e9ffce76791e83896fe84c40282566b69d794e6c80dabb7660c44	["*"]	2025-07-09 11:15:05	\N	2025-07-09 06:57:30	2025-07-09 11:15:05
65	App\\Models\\User	53	auth_token	bfeea05038176eb2cbc96e66f1534cf4773928a794f75732ece953533ee35710	["*"]	2025-07-18 06:17:52	\N	2025-07-17 09:24:08	2025-07-18 06:17:52
51	App\\Models\\User	39	auth_token	706aedb350a3b061a1a04c4c3cda781349db03984651de5866a379c3f6a3404c	["*"]	2025-07-14 11:17:16	\N	2025-07-14 11:16:20	2025-07-14 11:17:16
66	App\\Models\\User	53	auth_token	578f88231e3f4a3b377a9f74e1ae1c45a54d6b99a26085273e8540d29674b389	["*"]	2025-07-18 03:49:20	\N	2025-07-17 09:35:05	2025-07-18 03:49:20
61	App\\Models\\User	53	auth_token	017f8c314df1f8c76bcfa7d3b30944d2776829f963c46eeff4663b93ff51070c	["*"]	2025-07-17 06:17:44	\N	2025-07-17 06:17:39	2025-07-17 06:17:44
55	App\\Models\\User	47	auth_token	9eb320ca6d2d7170173cc540aefd9e533c318984bc492fc12eb8e8f8ce5ae663	["*"]	2025-07-15 06:45:14	\N	2025-07-14 11:38:01	2025-07-15 06:45:14
64	App\\Models\\User	52	auth_token	bfa42cecae62db1d6f35d13998b458bfb6b431ac463e258272ff99b39f403902	["*"]	2025-07-17 09:23:14	\N	2025-07-17 09:21:08	2025-07-17 09:23:14
67	App\\Models\\User	47	auth_token	6e485c9c169ca5730914eb298c04114b7e9be0848fd35ff5cfcb764097610724	["*"]	2025-07-18 06:04:39	\N	2025-07-18 04:51:19	2025-07-18 06:04:39
60	App\\Models\\User	52	auth_token	4e85d3dbe4c9bd3fdc056670b8d163c6a3ca9537a200dc80ecd797594c8daf0d	["*"]	2025-07-17 06:47:48	\N	2025-07-16 05:53:24	2025-07-17 06:47:48
79	App\\Models\\User	39	auth_token	0b287f2d338786a1ab2535cc1fa913a66128082b69ad0a87dd2d981f78e2f5d2	["*"]	2025-07-21 04:14:16	\N	2025-07-21 04:14:09	2025-07-21 04:14:16
71	App\\Models\\User	53	auth_token	ae3fe2462bee14e83e5932d61e42138ef5e604e7fba62d699602cb24e685b5cb	["*"]	2025-07-18 06:32:49	\N	2025-07-18 06:26:56	2025-07-18 06:32:49
80	App\\Models\\User	52	auth_token	00fc3b5de917adb54333878d1d1cec031549ed50ae91a7b5a2e24273f4a8bd13	["*"]	2025-07-21 07:55:00	\N	2025-07-21 04:14:40	2025-07-21 07:55:00
68	App\\Models\\User	53	auth_token	235b7d00bd81ba76b66a72b5becfcb4e1570a5c0a9491e6d31ae64e82660e95c	["*"]	2025-07-18 06:15:23	\N	2025-07-18 06:15:20	2025-07-18 06:15:23
70	App\\Models\\User	53	auth_token	261d57e069601ba0da62430a3443770ae4ac2f5f2774972981123daf848494f5	["*"]	2025-07-18 10:49:20	\N	2025-07-18 06:24:09	2025-07-18 10:49:20
75	App\\Models\\User	53	auth_token	cdf0b3c13c01c335b829c31523a9bd7bc4d4dd993c91c85e9cc869eeae4c0449	["*"]	2025-07-21 07:28:10	\N	2025-07-18 11:23:55	2025-07-21 07:28:10
76	App\\Models\\User	53	auth_token	7b4429eada085febb4585afcb25ada7cbd713ef8bf0ce4a60ee3b531006c3712	["*"]	2025-07-22 10:48:13	\N	2025-07-18 12:22:26	2025-07-22 10:48:13
78	App\\Models\\User	47	auth_token	c3801e8cb3709dd8322fac7243acf809a291057f6f209aed7e9109ab31550489	["*"]	2025-07-21 04:13:47	\N	2025-07-21 04:11:45	2025-07-21 04:13:47
84	App\\Models\\User	52	auth_token	97b536c6a7a7d7b09674c37a525f56180fbf8c07533441c86832e98e2fc2e9d2	["*"]	2025-07-22 10:14:18	\N	2025-07-22 04:02:57	2025-07-22 10:14:18
81	App\\Models\\User	47	auth_token	50e5b5852528235c0884a39d21aa8c4a79526e742ea4991c9b5863f40cef33a8	["*"]	2025-07-21 07:47:44	\N	2025-07-21 07:46:45	2025-07-21 07:47:44
82	App\\Models\\User	52	auth_token	d3b4ab760789fb5a398028591f79b81bef6006e0c8557a15848a6a1948521d55	["*"]	2025-07-21 08:36:40	\N	2025-07-21 08:36:15	2025-07-21 08:36:40
83	App\\Models\\User	47	auth_token	6df1892ec735cd36515dff4f766158bae1b08f69d33ace23922d2b8ea1f58d57	["*"]	2025-07-22 11:21:14	\N	2025-07-21 08:53:10	2025-07-22 11:21:14
85	App\\Models\\User	53	auth_token	6da373b4d6723d312ce3ea09678cfa95955cfe527ffbd449c999f5a5f21f3a0f	["*"]	2025-07-22 10:31:06	\N	2025-07-22 10:30:10	2025-07-22 10:31:06
77	App\\Models\\User	53	auth_token	39e66c0fa7c01758166ef6aa887836a0907962b67e04b58bd5c4585e1f1786eb	["*"]	2025-07-23 07:21:30	\N	2025-07-18 15:23:04	2025-07-23 07:21:30
87	App\\Models\\User	47	auth_token	e6dd91b51c1f334dbedbcdeab68efc05c67b2341d632c8995ecdceb3e86a27f9	["*"]	2025-07-22 10:49:01	\N	2025-07-22 10:48:34	2025-07-22 10:49:01
91	App\\Models\\User	57	auth_token	0eeec76f3fd9572f3f4236ee256356f2d06f5ae6e571fba43e1ce2b0b8fc35cd	["*"]	2025-07-23 06:22:16	\N	2025-07-23 06:08:06	2025-07-23 06:22:16
86	App\\Models\\User	55	auth_token	58a42e1eb15951f5de844452c4d41ce6ca19cc96562b1933534520353748333a	["*"]	2025-07-22 11:01:30	\N	2025-07-22 10:32:55	2025-07-22 11:01:30
100	App\\Models\\User	47	auth_token	a0b1c4dac72ee07ffde764bbaa75b0d4a657e294ce672c143885cabf4a7d8978	["*"]	2025-07-29 11:51:39	\N	2025-07-24 11:36:52	2025-07-29 11:51:39
89	App\\Models\\User	56	auth_token	35a018f3438b509c1a8fef180d19f6a22caa0dd3e985c7bf3b8dbb8f78987775	["*"]	2025-07-23 08:02:35	\N	2025-07-22 12:21:19	2025-07-23 08:02:35
94	App\\Models\\User	45	auth_token	3f6ebd46c9d07a0691cfb70ea05aed293a7e8e131a419b842eea41c5bea8adc9	["*"]	2025-07-28 07:38:41	\N	2025-07-23 07:21:37	2025-07-28 07:38:41
88	App\\Models\\User	47	auth_token	dcf1ede9fc4b0ffdb1c8515c11654898a1910db8f15a29b0d83cb7bf57224ca3	["*"]	2025-07-22 12:20:48	\N	2025-07-22 12:10:45	2025-07-22 12:20:48
92	App\\Models\\User	52	auth_token	49366f8d774432b06e0327880f322ca08e2f1ff1b8ed577b4b999dcae87e3066	["*"]	2025-07-23 06:35:24	\N	2025-07-23 06:22:35	2025-07-23 06:35:24
99	App\\Models\\User	52	auth_token	d58aab5036976e993d7272b4b6dce2ea9de5f74a0e67299299a7e297ca795358	["*"]	2025-07-29 11:53:11	\N	2025-07-23 14:41:37	2025-07-29 11:53:11
101	App\\Models\\User	50	auth_token	a091f0b95516fa30a9159cc40403d00fcff1985d1b66f3fdaa59d3964c743d38	["*"]	2025-07-27 19:05:41	\N	2025-07-27 18:46:32	2025-07-27 19:05:41
96	App\\Models\\User	56	auth_token	c21ec8ce1d0ac02bc25cfe2355cd2afc02446365ab2fe90e0038326bd3068d25	["*"]	2025-07-23 10:00:45	\N	2025-07-23 08:44:35	2025-07-23 10:00:45
97	App\\Models\\User	47	auth_token	69f6ea007cb071737603a3997f46ccca9a6010d6607516ba07ace542848a48a3	["*"]	2025-07-24 11:10:50	\N	2025-07-23 11:51:56	2025-07-24 11:10:50
93	App\\Models\\User	57	auth_token	766ba7841fbe4036ae6d438bae797201081ba72731f8ea30ecd8c6dd6b84605c	["*"]	2025-07-23 08:00:33	\N	2025-07-23 06:39:59	2025-07-23 08:00:33
90	App\\Models\\User	52	auth_token	bcdd615bda087074c9774ab24cf7f5609f65aee8b58509ca58a3a29b97f36b17	["*"]	2025-07-23 06:07:58	\N	2025-07-23 05:48:51	2025-07-23 06:07:58
98	App\\Models\\User	47	auth_token	d5ec1946995383dcc6783d0ec1c2c3a68885bd9859674c0bd6a8d339e6d218ee	["*"]	2025-07-23 14:40:15	\N	2025-07-23 13:52:26	2025-07-23 14:40:15
102	App\\Models\\User	47	auth_token	7334bc65f21d3699e1adf2417c972a4bd7e66f3c6a1d149c444f8814aec05bd7	["*"]	2025-07-29 11:50:59	\N	2025-07-29 05:46:24	2025-07-29 11:50:59
95	App\\Models\\User	52	auth_token	8b7168b3c04b0fa388d9d147849e0e57dfd7e2477941900e803f36a13fbb2cb5	["*"]	2025-07-23 13:48:34	\N	2025-07-23 08:03:09	2025-07-23 13:48:34
\.


--
-- Data for Name: product_balances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_balances (id, product_id, warehouse_id, quantity, created_at, updated_at) FROM stdin;
52	133	9	80	2025-07-26 07:44:23	2025-07-26 07:44:23
53	134	9	500	2025-07-26 07:44:25	2025-07-26 07:44:25
54	135	3	1000	2025-07-26 08:37:27	2025-07-26 08:37:27
5	73	9	10	2025-07-16 06:39:36	2025-07-16 06:39:36
6	74	9	5	2025-07-16 06:48:04	2025-07-16 06:48:05
7	75	9	10	2025-07-16 06:55:06	2025-07-16 06:55:07
65	67	15	1	2025-07-27 18:57:50	2025-07-27 18:57:50
73	151	9	25	2025-07-28 03:51:22	2025-07-28 03:51:22
74	126	16	3	2025-07-28 04:01:56	2025-07-28 04:11:45
44	126	9	12	2025-07-24 09:46:09	2025-07-28 04:11:47
31	106	13	2000	2025-07-22 12:22:00	2025-07-22 12:22:00
32	107	13	150	2025-07-22 12:22:00	2025-07-22 12:22:00
33	109	14	100	2025-07-23 06:42:55	2025-07-23 06:42:55
34	111	14	10	2025-07-23 07:11:21	2025-07-23 07:11:21
27	102	13	154	2025-07-22 12:22:00	2025-07-23 09:30:10
28	103	13	21	2025-07-22 12:22:00	2025-07-23 09:30:10
29	104	13	82	2025-07-22 12:22:00	2025-07-23 09:52:35
30	105	13	490	2025-07-22 12:22:00	2025-07-23 09:52:35
37	119	1	150	2025-07-23 12:01:47	2025-07-23 12:01:47
38	120	1	25	2025-07-23 12:01:47	2025-07-23 12:01:47
39	121	1	80	2025-07-23 12:01:47	2025-07-23 12:01:47
40	122	1	500	2025-07-23 12:01:47	2025-07-23 12:01:47
41	123	1	2000	2025-07-23 12:01:47	2025-07-23 12:01:47
42	124	1	150	2025-07-23 12:01:47	2025-07-23 12:01:47
19	94	9	2100	2025-07-21 07:26:16	2025-07-26 07:29:25
\.


--
-- Data for Name: product_fields; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_fields (id, user_id, field_name, created_at, updated_at) FROM stdin;
1	52	1234	\N	\N
2	52	5678	\N	\N
3	52	0000	\N	\N
4	47	Цвет	\N	\N
5	47	Ширина	\N	\N
\.


--
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_images (id, product_id, image_url, alt_text, created_at, updated_at) FROM stdin;
27	56	uploads/products/product_686be7a25ada84.51712061.webp		2025-07-07 15:28:34	2025-07-07 15:28:34
28	67	uploads/products/product_686cfa10292c90.27407105.webp		2025-07-08 10:59:28	2025-07-08 10:59:28
29	67	uploads/products/product_686cfa3fc53e72.77325935.webp		2025-07-08 11:00:15	2025-07-08 11:00:15
34	71	uploads/products/product_68750741aaa648.92287775.webp		2025-07-14 13:33:53	2025-07-14 13:33:53
36	73	uploads/products/product_6877480ee094f3.45032833.webp		2025-07-16 06:34:54	2025-07-16 06:34:54
37	74	uploads/products/product_68774a35a42ee4.08489224.webp		2025-07-16 06:44:05	2025-07-16 06:44:05
38	75	uploads/products/product_68774ca811fc51.43744419.webp		2025-07-16 06:54:32	2025-07-16 06:54:32
43	111	uploads/products/product_68808cbb542048.81604282.webp		2025-07-23 07:18:19	2025-07-23 07:18:19
56	122	uploads/products/product_6880cf50a2db12.79087061.webp		2025-07-23 12:02:24	2025-07-23 12:02:24
57	121	uploads/products/product_6880cf8537ee19.46739540.webp		2025-07-23 12:03:17	2025-07-23 12:03:17
58	120	uploads/products/product_6880cfa8c20f81.08499447.webp		2025-07-23 12:03:52	2025-07-23 12:03:52
59	124	uploads/products/product_6880cfc7428ef0.06475303.webp		2025-07-23 12:04:23	2025-07-23 12:04:23
60	119	uploads/products/product_6880cfebe2a9e6.53985079.webp		2025-07-23 12:05:00	2025-07-23 12:05:00
61	123	uploads/products/product_6880d00ebf7d75.93050248.webp		2025-07-23 12:05:34	2025-07-23 12:05:34
63	94	uploads/products/product_6880d3999cc905.48331573.webp		2025-07-23 12:20:41	2025-07-23 12:20:41
64	135	uploads/products/product_6884935963bba1.00399023.webp		2025-07-26 08:35:37	2025-07-26 08:35:37
\.


--
-- Data for Name: product_operations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_operations (id, product_id, warehouse_id, operation_type, quantity, reference_type, reference_id, notes, created_by, created_at, updated_at) FROM stdin;
1	45	1	receipt	5	receipt	1	Оприходование №1	47	2025-07-14 08:12:41	2025-07-14 08:12:41
2	43	1	receipt	1000	receipt	2	Оприходование №2	47	2025-07-14 08:43:54	2025-07-14 08:43:54
4	43	1	transfer_out	-400	product_transfer	4	Перемещение в склад: Запасной склад	47	2025-07-14 10:32:15	2025-07-14 10:32:15
5	43	3	transfer_in	400	product_transfer	4	Перемещение со склада: Главный склад	47	2025-07-14 10:32:17	2025-07-14 10:32:17
7	43	1	write_off	-100	write_off	10	Списание №1	47	2025-07-14 12:53:31	2025-07-14 12:53:31
8	73	9	receipt	10	receipt	3	Оприходование №AUTO-1752647709	52	2025-07-16 06:39:37	2025-07-16 06:39:37
9	74	9	receipt	5	receipt	4	Оприходование №AUTO-1752648262	52	2025-07-16 06:48:06	2025-07-16 06:48:06
10	75	9	receipt	10	receipt	5	Оприходование №AUTO-1752648899	52	2025-07-16 06:55:07	2025-07-16 06:55:07
14	102	13	receipt	2	inventory_receipt	13	Автоматическое оприходование по инвентаризации №ИНВ-ИЗБ-18-23072025	56	2025-07-23 09:30:10	2025-07-23 09:30:10
15	103	13	write_off	-2	inventory_write_off	13	Автоматическое списание по инвентаризации №ИНВ-СПИ-18-23072025	56	2025-07-23 09:30:10	2025-07-23 09:30:10
16	102	13	receipt	2	inventory_receipt	14	Автоматическое оприходование по инвентаризации №ИНВ-ИЗБ-18-23072025	56	2025-07-23 09:30:10	2025-07-23 09:30:10
17	103	13	write_off	-2	inventory_write_off	14	Автоматическое списание по инвентаризации №ИНВ-СПИ-18-23072025	56	2025-07-23 09:30:10	2025-07-23 09:30:10
18	104	13	receipt	2	inventory_receipt	15	Автоматическое оприходование по инвентаризации №ИНВ-ИЗБ-21-23072025	56	2025-07-23 09:52:35	2025-07-23 09:52:35
19	105	13	write_off	-10	inventory_write_off	15	Автоматическое списание по инвентаризации №ИНВ-СПИ-21-23072025	56	2025-07-23 09:52:35	2025-07-23 09:52:35
21	43	1	income	10	\N	\N	\N	52	2025-07-26 07:51:43	2025-07-26 07:51:43
22	43	1	expense	5	\N	\N	\N	52	2025-07-26 07:51:44	2025-07-26 07:51:44
28	67	15	receipt	1	receipt	27	Оприходование №345435	50	2025-07-27 18:57:50	2025-07-27 18:57:50
29	126	9	transfer_out	-5	product_transfer	5	Перемещение в склад: 222	52	2025-07-28 04:01:59	2025-07-28 04:01:59
30	126	16	transfer_in	5	product_transfer	5	Перемещение со склада: Склад 1	52	2025-07-28 04:02:01	2025-07-28 04:02:01
31	126	16	transfer_out	-2	product_transfer	6	Перемещение в склад: Склад 1	52	2025-07-28 04:11:49	2025-07-28 04:11:49
32	126	9	transfer_in	2	product_transfer	6	Перемещение со склада: 222	52	2025-07-28 04:11:51	2025-07-28 04:11:51
\.


--
-- Data for Name: product_transfer_positions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_transfer_positions (id, transfer_id, product_id, quantity, actual_quantity, notes, created_at, updated_at) FROM stdin;
5	5	126	5	5	\N	2025-07-28 04:01:51	2025-07-28 04:01:51
6	6	126	2	2	123	2025-07-28 04:11:43	2025-07-28 04:11:43
\.


--
-- Data for Name: product_transfers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.product_transfers (id, from_warehouse_id, to_warehouse_id, transfer_date, status, notes, created_by, created_at, updated_at, completed_at, completed_by) FROM stdin;
5	9	16	2025-07-28	completed	-	52	2025-07-28 04:01:50	2025-07-28 04:01:50	\N	\N
6	16	9	2025-07-28	completed	-	52	2025-07-28 04:11:42	2025-07-28 04:11:42	\N	\N
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.products (id, glink, title, title_ru, title_en, title_uz, titles, description, characters, vendore, main_photo_url, thumb, video_url, photos, out_photo, price, price_usd, count, min_order_count, country, sell_count, opt_price, discount, opt_discount, currency, price_with_nds, nds_percent, measure_unit, brand, active, moderated, owner_rating, order_count, company_name, user_id, category_id, subcategory_id, views, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: products_sklad; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.products_sklad (id, user_id, name, description, category, subcategory, country, supplier, article, code, external_code, unit, weight, volume, vat, min_stock, stock_type, packing, accounting_type, traceable, marking, product_type, barcode_type, barcode, cash_register_tax, cash_register_type, created_at, updated_at, warehouse_id, start_count, price, fields) FROM stdin;
123	47	Провод медный ВВГнг 3x2.5	\N	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	user_subcat_2bb828db-9a01-42fc-b2d9-e0605ca9d273	\N	\N	PROV-VVG-005	\N	\N	Метр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-29 11:33:15	1	2000	100.00	{"\\u0426\\u0432\\u0435\\u0442":null,"\\u0428\\u0438\\u0440\\u0438\\u043d\\u0430":null}
43	39	Кирпич строительный красный	Описание товара 8 для складского учета	\N	\N	США	ИП Иванов А.А.	СПО-6160	FG-4057	EXT-2935	Упаковка	5.500	4.535	0%	\N	\N	Весовая	Без специализированного учета	\N	\N	Упакованная вода	UPC	75849851073	ОСН	Товар	2025-07-07 07:42:32	2025-07-07 07:42:40	\N	0	0.00	\N
45	39	Диван угловой "Комфорт"	Описание товара 10 для складского учета	mebel-i-domashniy-dekor	mebel-dlya-doma-i-sada	Франция	ООО "ПродуктСервис"	АВТ-8730	VP-1904	EXT-9459	Грамм	9.370	4.808	10%	\N	\N	Штучная	Без специализированного учета	\N	\N	Не маркируется	EAN8	0022614699758	ОСН	Товар	2025-07-07 07:43:01	2025-07-07 07:49:36	\N	0	0.00	\N
102	56	Бетон М300 готовый	\N	stroitelstvo-i-nedvijimost	arkhitekturnie-oblomi	\N	\N	BTN-300-001	\N	\N	Метр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
103	56	Клей ПВА универсальный	\N	khimikati	klei-i-germetiki	\N	\N	KLEI-PVA-002	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
104	56	Краска акриловая белая	\N	khimikati	biologicheskie-khimicheskie-produkti	\N	\N	KRASKA-ACR-003	\N	\N	Литр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
56	48	Airpods 3	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-07 15:28:16	2025-07-07 15:28:16	\N	0	0.00	\N
105	56	Удобрение азотное NPK	\N	selskoe-khozyaystvo	vanilnie-bobi	\N	\N	UDOB-NPK-004	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
106	56	Провод медный ВВГнг 3x2.5	\N	stroitelstvo-i-nedvijimost	balyasini-i-perila	\N	\N	PROV-VVG-005	\N	\N	Метр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
107	56	Косметика увлажняющий крем	\N	krasota-i-lichnaya-gigiena	ukhod-za-kojey	\N	\N	KOSM-CREM-006	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-22 12:22:00	2025-07-22 12:22:00	13	0	0.00	\N
109	57	test	\N	avtomobili-i-aksessuary	avtobezopasnost-i-zashchita	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-23 06:41:39	2025-07-23 06:42:53	14	100	0.00	\N
111	57	тест2	\N	avtomobili-i-aksessuary	avtobezopasnost-i-zashchita	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-23 07:05:54	2025-07-23 07:11:20	14	10	50.00	\N
71	45	Редактирование "Клапан Канал-Регуляр"	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-14 13:33:45	2025-07-14 13:33:45	\N	0	0.00	\N
122	47	Удобрение азотное NPK	\N	selskoe-khozyaystvo	drugie-selkhoz-produkti	\N	\N	UDOB-NPK-004	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-23 13:29:12	1	500	1000.00	\N
121	47	Краска акриловая белая	\N	khimikati	biologicheskie-khimicheskie-produkti	\N	\N	KRASKA-ACR-003	\N	\N	Литр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-23 13:37:56	1	80	1000.00	\N
124	47	Косметика увлажняющий крем	\N	krasota-i-lichnaya-gigiena	ukhod-za-kojey	\N	\N	KOSM-CREM-006	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-23 13:38:16	1	150	500.00	\N
120	47	Клей ПВА универсальный	\N	khimikati	klei-i-germetiki	\N	\N	KLEI-PVA-002	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-23 13:38:08	1	25	1000.00	\N
94	52	Провод медный ВВГнг 3x2.5	Хороший провод	\N	avtobezopasnost-i-zashchita	\N	\N	PROV-VVG-005	\N	\N	Метр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-21 07:26:09	2025-07-26 07:29:23	9	2100	45.00	{"1234":null,"5678":null,"0000":null}
126	52	тест	описание тест	\N	avtobezopasnost-i-zashchita	\N	\N	артикул тест	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-24 09:45:19	2025-07-26 07:34:27	9	15	100.00	{"1234":"1234 0","5678":"5678 0","0000":"0000 0"}
119	47	Бетон М300 готовый	\N	stroitelstvo-i-nedvijimost	prochee-stroitelstvo-i-nedvijimost	\N	\N	BTN-300-001	\N	\N	Метр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-23 12:01:47	2025-07-23 14:37:30	1	150	700.00	\N
135	47	Test	test product	avtomobili-i-aksessuary	avtobezopasnost-i-zashchita	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-26 08:35:20	2025-07-26 08:37:27	3	1000	100.00	{"\\u0426\\u0432\\u0435\\u0442":null,"\\u0428\\u0438\\u0440\\u0438\\u043d\\u0430":null}
67	50	Тестовое описание товара для быстрого тестирования функционала	test	bezopasnost-i-zatshita	videonablyudenie	\N	ucell telecom	test	334523	3425355	Тонна	32.000	33.000	233	\N	\N	\N	\N	\N	\N	\N	\N	43	43	34	2025-07-08 10:59:20	2025-07-27 18:51:17	\N	4545	4545.00	[]
73	52	Iphone 15 pro max	\N	\N	\N	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-16 06:34:39	2025-07-16 06:35:08	9	0	0.00	\N
74	52	Macbook air M2	\N	\N	\N	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-16 06:43:39	2025-07-16 06:44:21	9	0	0.00	\N
75	52	Apple display pro xdr	\N	\N	\N	\N	\N	\N	\N	\N	Штука	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2025-07-16 06:54:15	2025-07-16 06:54:58	9	0	0.00	\N
133	52	Краска акриловая белая	\N	\N	\N	\N	\N	KRASKA-ACR-003	\N	\N	Литр	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-26 07:44:20	2025-07-26 07:44:20	9	80	0.00	\N
134	52	Удобрение азотное NPK	\N	\N	\N	\N	\N	UDOB-NPK-004	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-26 07:44:21	2025-07-26 07:44:21	9	500	0.00	\N
151	52	Клей ПВА универсальный	\N	\N	\N	\N	\N	KLEI-PVA-002	\N	\N	Килограмм	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	Товар	2025-07-28 03:51:17	2025-07-28 03:51:17	9	25	0.00	\N
\.


--
-- Data for Name: receipt_files; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.receipt_files (id, receipt_id, filename, size_mb, uploaded_at, employee, file_url) FROM stdin;
1	27	товары_2025-07-27_23-50-15.xlsx	0.02	2025-07-27 18:57:50	Rudolf7	https://api.b2bstorage.ru/storage/uploads/receipts/receipt_6886767cf2ad84.08264672.xlsx
\.


--
-- Data for Name: receipt_positions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.receipt_positions (id, receipt_id, name, code, barcode, article, quantity, balance, price, amount, reason, gtd, rnpt, country, product_id, created_at, updated_at) FROM stdin;
7	3	Iphone 15 pro max				10	10.000	100000.00	1000000.00					73	2025-07-16 06:39:31	2025-07-16 06:39:31
9	4	Macbook air M2				5	5.000	140000.00	700000.00					74	2025-07-16 06:48:00	2025-07-16 06:48:00
10	5	Apple display pro xdr	\N	\N	\N	10	10.000	400000.00	4000000.00	\N	\N	\N	\N	75	2025-07-16 06:55:01	2025-07-16 06:55:01
25	9	Провод медный ВВГнг 3x2.5	\N	\N	PROV-VVG-005	2000	0.000	45.00	90000.00	\N	\N	\N	\N	94	2025-07-21 07:26:12	2025-07-21 07:26:12
33	11	Бетон М300 готовый	\N	\N	BTN-300-001	150	0.000	4500.00	675000.00	\N	\N	\N	\N	102	2025-07-22 12:22:00	2025-07-22 12:22:00
34	11	Клей ПВА универсальный	\N	\N	KLEI-PVA-002	25	0.000	120.00	3000.00	\N	\N	\N	\N	103	2025-07-22 12:22:00	2025-07-22 12:22:00
35	11	Краска акриловая белая	\N	\N	KRASKA-ACR-003	80	0.000	350.00	28000.00	\N	\N	\N	\N	104	2025-07-22 12:22:00	2025-07-22 12:22:00
36	11	Удобрение азотное NPK	\N	\N	UDOB-NPK-004	500	0.000	85.00	42500.00	\N	\N	\N	\N	105	2025-07-22 12:22:00	2025-07-22 12:22:00
37	11	Провод медный ВВГнг 3x2.5	\N	\N	PROV-VVG-005	2000	0.000	45.00	90000.00	\N	\N	\N	\N	106	2025-07-22 12:22:00	2025-07-22 12:22:00
38	11	Косметика увлажняющий крем	\N	\N	KOSM-CREM-006	150	0.000	280.00	42000.00	\N	\N	\N	\N	107	2025-07-22 12:22:00	2025-07-22 12:22:00
42	15	Краска акриловая белая	\N	\N	KRASKA-ACR-003	2	0.000	0.00	0.00	Избыток по инвентаризации №21	\N	\N	\N	104	2025-07-23 09:52:35	2025-07-23 09:52:35
44	17	Кирпич строительный красный	\N	\N	СПО-6160	5	0.000	0.00	0.00	Избыток по инвентаризации №40	\N	\N	\N	43	2025-07-26 07:54:11	2025-07-26 07:54:11
45	18	Кирпич строительный красный	\N	\N	СПО-6160	5	0.000	0.00	0.00	Избыток по инвентаризации №41	\N	\N	\N	43	2025-07-26 07:55:44	2025-07-26 07:55:44
48	21	Удобрение азотное NPK	\N	\N	UDOB-NPK-004	10	0.000	0.00	0.00	Избыток по инвентаризации №44	\N	\N	\N	134	2025-07-26 08:04:58	2025-07-26 08:04:58
49	22	Бетон М300 готовый	\N	\N	BTN-300-001	10	0.000	700.00	7000.00	Избыток по инвентаризации №46	\N	\N	\N	119	2025-07-26 08:52:22	2025-07-26 08:52:22
51	24	Бетон М300 готовый	\N	\N	BTN-300-001	1	0.000	700.00	700.00	Избыток по инвентаризации №50	\N	\N	\N	119	2025-07-26 11:48:00	2025-07-26 11:48:00
53	25	Бетон М300 готовый	\N	\N	BTN-300-001	10	0.000	700.00	7000.00	Избыток по инвентаризации №51	\N	\N	\N	119	2025-07-26 11:49:31	2025-07-26 11:49:31
54	25	Клей ПВА универсальный	\N	\N	KLEI-PVA-002	5	0.000	1000.00	5000.00	Избыток по инвентаризации №51	\N	\N	\N	120	2025-07-26 11:49:31	2025-07-26 11:49:31
55	25	Краска акриловая белая	\N	\N	KRASKA-ACR-003	5	0.000	1000.00	5000.00	Избыток по инвентаризации №51	\N	\N	\N	121	2025-07-26 11:49:31	2025-07-26 11:49:31
57	27	Тестовое описание товара для быстрого тестирования функционала	334523		test	1	0.000	0.00	0.00					67	2025-07-27 18:57:50	2025-07-27 18:57:50
\.


--
-- Data for Name: receipt_tasks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.receipt_tasks (id, receipt_id, task, created_at) FROM stdin;
\.


--
-- Data for Name: receipts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.receipts (id, number, date, organization, project, warehouse, status, is_posted, comment, total, overhead_costs, created_by, created_at, user_id, updated_at) FROM stdin;
8	3	2025-07-21 07:16:00		\N	9	posted	t	\N	0.00	0.00	52	2025-07-21 07:16:00	52	2025-07-21 07:16:00
9	2025-07-21_07:26:11	2025-07-21 07:26:11		\N	9	posted	t	\N	132000.00	0.00	52	2025-07-21 07:26:11	52	2025-07-21 07:26:13
3	AUTO-1752647709	2025-07-16 06:35:00	EM WA	\N	9	posted	\N	\N	1000000.00	0.00	52	2025-07-16 06:35:09	52	2025-07-16 06:39:28
4	AUTO-1752648262	2025-07-16 06:44:00	EM WA	\N	9	posted	\N	\N	700000.00	0.00	52	2025-07-16 06:44:22	52	2025-07-16 06:47:57
5	AUTO-1752648899	2025-07-16 06:54:59	Автоматическое оприходование	\N	9	posted	t	\N	4000000.00	0.00	52	2025-07-16 06:54:59	52	2025-07-16 06:55:02
25	ИНВ-ИЗБ-51-26072025	2025-07-26 11:49:31	Автоматическое оприходование по инвентаризации: 111	Инвентаризация	1	posted	t	Автоматическое оприходование по инвентаризации №51	17100.00	0.00	Edward McCain	2025-07-26 11:49:31	47	2025-07-26 11:49:31
11	2025-07-22_12:22:00	2025-07-22 12:22:00		\N	13	posted	t	\N	880500.00	0.00	56	2025-07-22 12:22:00	56	2025-07-22 12:22:00
15	ИНВ-ИЗБ-21-23072025	2025-07-23 09:52:35	Автоматическое оприходование по инвентаризации: 11111	Инвентаризация	13	posted	t	Автоматическое оприходование по инвентаризации №21	0.00	0.00	edmccain0333@gmail.com	2025-07-23 09:52:35	56	2025-07-23 09:52:35
17	ИНВ-ИЗБ-40-26072025	2025-07-26 07:54:10	Автоматическое оприходование по инвентаризации: Тестовая инвентаризация с избытком	Инвентаризация	1	posted	t	Автоматическое оприходование по инвентаризации №40	0.00	0.00	Edward McCain	2025-07-26 07:54:10	52	2025-07-26 07:54:10
18	ИНВ-ИЗБ-41-26072025	2025-07-26 07:55:43	Автоматическое оприходование по инвентаризации: Тестовая инвентаризация с избытком	Инвентаризация	1	posted	t	Автоматическое оприходование по инвентаризации №41	0.00	0.00	Edward McCain	2025-07-26 07:55:43	52	2025-07-26 07:55:43
26	1111	2025-07-26 19:24:00	-	\N	1	posted	f	\N	100000.00	0.00	Edward McCain	2025-07-26 12:24:45	47	2025-07-26 12:24:45
27	345435	2025-07-27 23:53:00	kaka	sdfds	15	posted	f	34rkfldklf	343.00	343.00	Rudolf7	2025-07-27 18:57:50	50	2025-07-27 18:57:50
21	ИНВ-ИЗБ-44-26072025	2025-07-26 08:04:57	Автоматическое оприходование по инвентаризации: тест авто списаний и оприходований	Инвентаризация	9	posted	t	Автоматическое оприходование по инвентаризации №44	0.00	0.00	Edward McCain	2025-07-26 08:04:57	52	2025-07-26 08:04:57
22	ИНВ-ИЗБ-46-26072025	2025-07-26 08:52:22	Автоматическое оприходование по инвентаризации: test	Инвентаризация	1	posted	t	Автоматическое оприходование по инвентаризации №46	7000.00	0.00	Edward McCain	2025-07-26 08:52:22	47	2025-07-26 08:52:22
23	1125	2025-07-26 15:57:00	-	-	3	posted	f	\N	100300.00	300.00	Edward McCain	2025-07-26 08:57:30	47	2025-07-26 08:57:30
24	ИНВ-ИЗБ-50-26072025	2025-07-26 11:48:00	Автоматическое оприходование по инвентаризации: lkjlk	Инвентаризация	1	posted	t	Автоматическое оприходование по инвентаризации №50	700.00	0.00	Edward McCain	2025-07-26 11:48:00	47	2025-07-26 11:48:00
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
lWTK6daIHsQvROvkWiZ43v1EWnLNppi1kW6DPiva	\N	206.168.34.75	Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)	YTozOntzOjY6Il90b2tlbiI7czo0MDoiVE9vT21jemxrV1ZQQzdNTFZDeFpnZ3pVVEFac2ROYVNmbjgwUmdGeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753773642
4PpTBDwK7G1oAllx9bxCNeDgnYLfq8Bq8R2irgpB	\N	43.135.138.128	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmdJVDk5eDdaYjBCR0RQZTlvV1ZyZlBSSE5SWTFYQlNjY3V2aVNOaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753779843
pz3im6uRpRJWJr0T7JEGYx8Xh6rOAsD6SwCTyO3V	\N	109.73.196.179	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoic3d1SFFLVmRjYUtEbnVvZ0dFM05TZkJ0c0NaNUNTNzZjamJseE1FcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753788531
QC53zZrz0bMbKeoBPDIT7wDVTaGKdVbeZ5n1BLxn	\N	146.190.253.176	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0	YTozOntzOjY6Il90b2tlbiI7czo0MDoieDk5QmRUdG9ETDRjUUc1bTBYa1Q0YlE2UENjVFRYNmFzb1Bza1FoTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753773693
SZTtZiD5G8mjxjDPtIvAmako3JZYRY4syEXPrIpM	\N	47.237.115.100	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiTktLWTVRVm1kNFRFVXhEZ2tHaFI4UW1xOXNmTDJDNk5ndW1SNUlFTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753782797
XQqomltQNmaeOGurAdCDevJ9A3MjMaPqjgODikRl	\N	43.157.53.115	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0hwOWxuNEZCMHV2cDdtYlN5VzZrVlJtSVFMbWlqWk84MWNTeVoweiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753789399
HIMbVuFt1v8Lf6NaCliGky9NEuaOdQvibeihgYVr	\N	45.79.181.223	Mozilla/5.0 (Macintosh; Intel Mac OS X 13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiOTdKWm5Hdm9YNFhNU0RQT25KTTRyaWQzdEQ5YlRTNTFBbVFMTmRqYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753773912
cJB4M387C4hg2yczwIsuEMDJyOSEs7FwFGu5VOp0	\N	185.177.72.49	python-httpx/0.28.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoiSnlNdzBEdk5obzVyeGQ1alFJZnZsaWdidWNsZVk1YVhwUm1IYkV0QiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753784524
I6F62lchjHMMAyshPMaI7gPrM7z5LQqx95tLQU69	\N	43.130.34.74	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoielJLbUVHbXNZRWNuWFBEQW05dnJrTWluRW40RnNDdXZJQkpDbmZRWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753773977
OMfgMHGhGQNlKEREIcphtx0CjbwCAcdAWLj83ksl	\N	92.55.190.215	Custom-AsyncHttpClient	YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEdZQklvNHVzWm43Z3JheGVqaTdaalowZ2laSzA4aDU4akhtODluZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTUxOiJodHRwczovL2FwaS5iMmJzdG9yYWdlLnJ1L2luZGV4LnBocD9mdW5jdGlvbj1jYWxsX3VzZXJfZnVuY19hcnJheSZzPSUyRmluZGV4JTJGJTVDdGhpbmslNUNhcHAlMkZpbnZva2VmdW5jdGlvbiZ2YXJzJTVCMCU1RD1tZDUmdmFycyU1QjElNUQlNUIwJTVEPUhlbGxvIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==	1753784794
16YqwGYlZ2TfQDtCQYzjU2KCXmpFlSPJe7E7sS1x	\N	162.216.150.250	Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity	YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFduZ2wxYUtLckdXeW9QM0l0eUNKWGVLNldEaHR5cmN4Qlh6NHNRbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753775503
hw2Z5hvjHCoUaLP4XT2q4L4ZnX6upUEkZHNpsGXk	\N	92.55.190.215	Custom-AsyncHttpClient	YTozOntzOjY6Il90b2tlbiI7czo0MDoiTnpBcWhjanJKVlBkU1Mwc2NKbzk1ZUVVdjhta0QzakJJWHdJTjlzeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjA0OiJodHRwczovL2FwaS5iMmJzdG9yYWdlLnJ1L2luZGV4LnBocD8lMkYlM0MlM0ZlY2hvJTI4bWQ1JTI4JTIyaGklMjIlMjklMjklM0IlM0YlM0UlMjAlMkZ0bXAlMkZpbmRleDEucGhwPSZjb25maWctY3JlYXRlJTIwJTJGPSZsYW5nPS4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkZ1c3IlMkZsb2NhbCUyRmxpYiUyRnBocCUyRnBlYXJjbWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753784795
VaZ9OKjUvOkzCNzethQF2bwAsxAewFH3bp0evTAA	\N	35.203.210.111	Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity	YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1NsYndVVlNENldrRmFMaXMzeGpVSE0zU3d0SVlYQTVZMmFPTUV0QyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753777290
2TWmm76uqEIm5Jyfl8icMJV0nqHFzvqo5N37XV5j	\N	92.55.190.215	Custom-AsyncHttpClient	YTozOntzOjY6Il90b2tlbiI7czo0MDoiS1diN3BlSWtZWGdmR2REbTZtajVUbGt6ZkZPY3NGWXh4MFBibzlhNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6OTM6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUvaW5kZXgucGhwP2xhbmc9Li4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRi4uJTJGLi4lMkYuLiUyRnRtcCUyRmluZGV4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1753784796
hSt2Az27qNaOPoPu0MMhyJvM8y2AI9ZzdlEYj1SH	\N	45.156.128.45	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGRaRjNSN1hLSTZMZTlZSFFrZDZXTmI2TVRxV2xEdm1XS0VrMllrcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753767815
krL53qDmLo4SrmC9T7IlwSEIK7Mw8wcwhjmp1kzB	\N	198.235.24.186	Hello from Palo Alto Networks, find out more about our scans in https://docs-cortex.paloaltonetworks.com/r/1/Cortex-Xpanse/Scanning-activity	YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2tYWFVOYWNlSm9NV1ZoQjFHT1JqMlJTTE9hN0xnekwwb3pvcmRvNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753767861
Ryr7x8VgJJ0NSDc1g9AeDLkoM0LAkVbksQ3j5ZHR	\N	185.242.226.75	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiWGJZUktlQXhCOHE3QVlSVXBrOWFCcGR1MjY0RXo4cWhGTDA0TVgyNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753767923
kufvMSWqByqYtd3iIoDCM1vinDg18UrIUGzZuCED	\N	137.184.100.71	Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 10.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729)	YTozOntzOjY6Il90b2tlbiI7czo0MDoiSVRYVjlUaGhmdUY2S1oxWmVwdmpDaHdhVWkxVzhBcEJicWNDdmxUbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753767928
IMlT9O29D2750Q3N9sFjTvy2Sf750H1Gvof6qkoP	\N	164.52.0.92	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiNHR2SXBpQmFvV3RNdXRKekdIRzFQMGIwQmljY0pSeGphTHRpdEphViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753769114
5kZhwknJyANjOy7M7639oMJX5PrJixLBB2nzqFGR	\N	164.52.0.92		YTozOntzOjY6Il90b2tlbiI7czo0MDoid1k1SlJoSmZJZTNKOHZQbWlkM0pGSUZjRVk5bWRsRDVhdkxSWWxUUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753769118
VWG5liLoDHjsCUKzXhwIdlW4ucEkwo1wWbc9t5fP	\N	129.226.93.214	Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1	YTozOntzOjY6Il90b2tlbiI7czo0MDoia2xJcU5IVzFUWWJUdEJUbjNCaHdCRUxhQjZOek1URDNNTXFJWmRLNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753771922
5qaWJv2QOcgZUxNBwEbAEk1XxRR0tkxazc4WV1sT	\N	138.68.174.211	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0	YTozOntzOjY6Il90b2tlbiI7czo0MDoiaHZuTmZqZThFN0VuVkNXV2VCTGowVHgxaW5VeGVTYm9jbzdZMnkzZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753773375
6LNuMysMMCfNZRK97MMTHHmejJP8ZosMgMkW7fOL	\N	185.242.226.82	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.190 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTZKeEYzV0JuZTUyVkw0VndrSzFXamtUaEtqYklGd29nZEJvdzRhcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHBzOi8vYXBpLmIyYnN0b3JhZ2UucnUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753777309
ks6zocYASLg4Yf4yrfLpRDLsKeE7xqAIrPcjor3r	\N	85.142.100.139	Mozilla/5.0 (compatible; CyberOKInspect/1.0; +https://www.cyberok.ru/policy.html)	YTozOntzOjY6Il90b2tlbiI7czo0MDoibUJBNk9wV0dReWpLOFhNenhBTXlIQ3lSTGoxenNBUHlvRlNKbmUxQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTk6Imh0dHBzOi8vNS4zNS44NS4xMTAiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1753786625
\.


--
-- Data for Name: subcategories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.subcategories (id, subcategory_id, name, category_id, created_at, updated_at, product_count, name_en, name_ru, name_uz) FROM stdin;
120	agrokhimikati	Агрохимикаты	khimikati	2024-10-17 13:29:58	\N	8	Agrochemicals	Агрохимикаты	Agrokimyoviy moddalar
121	biologicheskie-khimicheskie-produkti	Биологические химические продукты	khimikati	2024-10-17 13:29:58	\N	0	Biological chemical products	Биологические химические продукты	Biologik kimyoviy mahsulotlar
122	vkusi-i-aromati	Вкусы и ароматы	khimikati	2024-10-17 13:29:58	\N	0	Tastes and aromas	Вкусы и ароматы	Ta'm va aromatlar
123	dobavki-i-additivi	Добавки и аддитивы	khimikati	2024-10-17 13:29:58	\N	2	Additives and supplements	Добавки и аддитивы	Qo'shimchalar va qo'shimchalar
124	katalizatori-i-khimicheskie-vspomogatelnie-vetshestva	Катализаторы и химические вспомогательные вещества	khimikati	2024-10-17 13:29:58	\N	0	Catalysts and chemical auxiliaries	Катализаторы и химические вспомогательные вещества	Katalizatorlar va kimyoviy yordamchilar
125	klei-i-germetiki	Клеи и герметики	khimikati	2024-10-17 13:29:58	\N	12	Adhesives and sealants	Клеи и герметики	Yopishtiruvchi va mastiklar
126	nevzrivchatoe-razrushayutshee-vetshestvo	Невзрывчатое разрушающее вещество	khimikati	2024-10-17 13:29:58	\N	0	Non-explosive destructive substance	Невзрывчатое разрушающее вещество	Portlovchi bo'lmagan vayron qiluvchi modda
127	neorganicheskie-khimicheskie-vetshestva	Неорганические химические вещества	khimikati	2024-10-17 13:29:58	\N	1	Inorganic chemicals	Неорганические химические вещества	Noorganik kimyoviy moddalar
128	organicheskiy-intermediat	Органический интермедиат	khimikati	2024-10-17 13:29:58	\N	0	Organic intermediate	Органический интермедиат	Organik oraliq mahsulot
129	osnovnie-organicheskie-khimicheskie-vetshestva	Основные органические химические вещества	khimikati	2024-10-17 13:29:58	\N	2	Basic organic chemicals	Основные органические химические вещества	Asosiy organik kimyoviy moddalar
130	pigment-i-krasitel	Пигмент и краситель	khimikati	2024-10-17 13:29:58	\N	0	Pigment and dye	Пигмент и краситель	Pigment va bo'yoq
131	povsednevnie-khimikati	Повседневные химикаты	khimikati	2024-10-17 13:29:58	\N	1	Everyday chemicals	Повседневные химикаты	Kundalik kimyoviy moddalar
132	pokraska-i-pokritie	Покраска и покрытие	khimikati	2024-10-17 13:29:58	\N	3	Painting and coating	Покраска и покрытие	Bo'yash va bo'yash
133	polimeri	Полимеры	khimikati	2024-10-17 13:29:58	\N	3	Polymers	Полимеры	Polimerlar
134	khimikati-dlya-obrabotki-poverkhnosti	Химикаты для обработки поверхности	khimikati	2024-10-17 13:29:58	\N	0	Surface Treatment Chemicals	Химикаты для обработки поверхности	Yuzaki ishlov berish uchun kimyoviy moddalar
135	khimicheskie-reaktivi-i-reagenti	Химические реактивы и реагенты	khimikati	2024-10-17 13:29:58	\N	1	Chemical reagents and reactants	Химические реактивы и реагенты	Kimyoviy reaktivlar va reaktivlar
136	drugie-khimicheskie-vetshestva	Другие химические вещества	khimikati	2024-10-17 13:29:58	\N	23	Other chemicals	Другие химические вещества	Boshqa kimyoviy moddalar
190	alyuminiy	Алюминий	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Aluminum	Алюминий	alyuminiy
191	voloknistie-produkti	Волокнистые продукты	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Fibrous foods	Волокнистые продукты	Tolali ovqatlar
192	volfram	Вольфрам	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Tungsten	Вольфрам	Volfram
193	grafitovie-izdeliya	Графитовые изделия	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Graphite products	Графитовые изделия	Grafit mahsulotlari
194	jelezo	Железо	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Iron	Железо	Temir
195	izvest	Известь	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Lime	Известь	Laym
196	izdeliya-iz-keramicheskogo-volokna	Изделия из керамического волокна	minerali-i-metallurgiya	2024-10-17 13:29:58	\N	0	Ceramic fiber products	Изделия из керамического волокна	Seramika tolasi mahsulotlari
197	izdeliya-iz-steklovolokna	Изделия из стекловолокна	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Fiberglass products	Изделия из стекловолокна	Fiberglas mahsulotlari
198	kvartsevie-izdeliya	Кварцевые изделия	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Quartz products	Кварцевые изделия	Kvarts mahsulotlari
199	keramika	Керамика	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	1	Ceramics	Керамика	Keramika
200	kolyuchaya-provoloka	Колючая проволока	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Barbed wire	Колючая проволока	Tikanli sim
201	liteynie-polufabrikati	Литейные полуфабрикаты	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Foundry semi-finished products	Литейные полуфабрикаты	Quyma yarim tayyor mahsulotlar
202	lite-i-kovka	Литье и ковка	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Casting and forging	Литье и ковка	Quyma va zarb qilish
203	magnitnie-materiali	Магнитные материалы	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Magnetic materials	Магнитные материалы	Magnit materiallar
204	med1	Медь	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Copper	Медь	Mis
205	metallicheskie-pliti	Металлические плиты	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Metal plates	Металлические плиты	Metall plitalar
206	metallolom	Металлолом	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Scrap metal	Металлолом	Metall parchalari
207	mineralnaya-vata	Минеральная вата	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Mineral wool	Минеральная вата	Mineral jun
208	molibden	Молибден	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Molybdenum	Молибден	Molibden
209	nemetallicheskie-poleznie-iskopaemie	Неметаллические полезные ископаемые	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Non-metallic minerals	Неметаллические полезные ископаемые	Metall bo'lmagan minerallar
210	nikel	Никель	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Nickel	Никель	Nikel
211	ogneupornie	Огнеупорные	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Fireproof	Огнеупорные	Yong'inga chidamli
212	provolochnaya-setka	Проволочная сетка	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Wire mesh	Проволочная сетка	Tel to'r
213	prochie-nemetallicheskie-minerali-i-produkti	Прочие неметаллические минералы и продукты	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Other non-metallic minerals and products	Прочие неметаллические минералы и продукты	Boshqa metall bo'lmagan minerallar va mahsulotlar
214	redkozemelnie-produkti	Редкоземельные продукты	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Rare earth products	Редкоземельные продукты	Noyob tuproq mahsulotlari
215	ruda	Руда	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Ore	Руда	ruda
216	svinets	Свинец	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Lead	Свинец	Qo'rg'oshin
217	slitki	Слитки	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Ingots	Слитки	Quymalar
218	stal	Сталь	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	1	Steel	Сталь	Chelik
219	steklo	Стекло	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Glass	Стекло	Shisha
220	titan	Титан	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Titanium	Титан	Titan
221	uglerod	Углерод	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Carbon	Углерод	Uglerod
222	tsementirovanniy-karbid	Цементированный карбид	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Cemented carbide	Цементированный карбид	Sementlangan karbid
223	tsink	Цинк	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Zinc	Цинк	Sink
224	chugun	Чугун	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Cast iron	Чугун	Quyma temir
225	drugie-metalli-i-metalloproduktsiya	Другие металлы и металлопродукция	minerali-i-metallurgiya	2024-10-17 13:29:59	\N	0	Other metals and metal products	Другие металлы и металлопродукция	Boshqa metallar va metall buyumlar
270	aromat-i-dezodorant	Аромат и дезодорант	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Fragrance and deodorant	Ароматизатор и дезодорант	Xushbo'y hid va dezodorant
271	bannie-prinadlejnosti	Банные принадлежности	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Bath accessories	Банные принадлежности	Hammom uchun aksessuarlar
272	brite-i-depilyatsiya	Бритье и депиляция	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Shaving and depilation	Бритье и депиляция	Soqol olish va depilatsiya
273	gigiena-polosti-rta	Гигиена полости рта	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Oral hygiene	Гигиена полости рта	Og'iz bo'shlig'i gigienasi
274	jenskaya-gigiena	Женская гигиена	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Feminine hygiene	Женская гигиена	Ayol gigienasi
275	instrumenti-dlya-makiyaja	Инструменты для макияжа	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Makeup tools	Инструменты для макияжа	Makiyaj vositalari
276	instrumenti-dlya-ukhoda-za-kojey	Инструменты для ухода за кожей	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Skin Care Tools	Инструменты для ухода за кожей	Terini parvarish qilish vositalari
277	kosmetologicheskoe-oborudovanie	Косметологическое оборудование	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	1	Cosmetology equipment	Косметологическое оборудование	Kosmetologiya uchun uskunalar
278	makiyaj	Макияж	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Makeup	Макияж	Grim surmoq, pardoz qilmoq; yasamoq, tuzmoq
279	naratshivanie-volos-i-pariki	Наращивание волос и парики	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Hair extensions and wigs	Наращивание волос и парики	Soch kengaytmalari va pariklar
280	sanitarnaya-bumaga	Санитарная бумага	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	1	Sanitary paper	Санитарная бумага	Sanitariya qog'ozi
281	tovari-dlya-nogtey	Товары для ногтей	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Nail products	Товары для ногтей	Tirnoq mahsulotlari
282	ukhod-za-volosami-i-ukladka	Уход за волосами и укладка	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Hair care and styling	Уход за волосами и укладка	Sochni parvarish qilish va shakllantirish
283	ukhod-za-grudyu	Уход за грудью	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Breast care	Уход за грудью	Ko'krak parvarishi
284	ukhod-za-kojey	Уход за кожей	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	0	Skin care	Уход за кожей	Terini parvarish qilish
285	prochie-tovari-dlya-krasoti-i-lichnoy-gigieni	Прочие товары для красоты и личной гигиены	krasota-i-lichnaya-gigiena	2024-10-17 13:30:03	\N	2	Other beauty and personal care products	Прочие товары для красоты и личной гигиены	Boshqa go'zallik va shaxsiy parvarish mahsulotlari
375	bankovskoe-oborudovanie	Банковское оборудование	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	1	Banking equipment	Банковское оборудование	Bank uskunalari
376	gruzovoe-i-skladskoe-oborudovanie	Грузовое и складское оборудование	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Cargo and warehouse equipment	Грузовое и складское оборудование	Yuk va ombor uskunalari
377	kommercheskoe-prachechnoe-oborudovanie	Коммерческое прачечное оборудование	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Commercial Laundry Equipment	Коммерческое прачечное оборудование	Tijorat kir yuvish uskunalari
378	oborudovanie-dlya-vistavok	Оборудование для выставок	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Exhibition equipment	Оборудование для выставок	Ko'rgazma uskunalari
379	pokhoronnie-prinadlejnosti	Похоронные принадлежности	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Funeral accessories	Похоронные принадлежности	Dafn marosimi uchun aksessuarlar
1647	barnaya-posuda	Барная посуда	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	0	Barware	Барная посуда	Barware
380	prinadlejnosti-dlya-gostinits-i-restoranov	Принадлежности для гостиниц и ресторанов	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	1	Hotel and Restaurant Supplies	Принадлежности для гостиниц и ресторанов	Mehmonxona va restoran uchun materiallar
381	prinadlejnosti-dlya-magazinov-i-supermarketov	Принадлежности для магазинов и супермаркетов	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Accessories for shops and supermarkets	Принадлежности для магазинов и супермаркетов	Do'konlar va supermarketlar uchun aksessuarlar
382	reklamnoe-oborudovanie	Рекламное оборудование	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Advertising equipment	Рекламное оборудование	Reklama uskunalari
383	svadebnie-prinadlejnosti	Свадебные принадлежности	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Wedding Accessories	Свадебные принадлежности	To'y aksessuarlari
384	torgovie-avtomati	Торговые автоматы	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Vending machines	Торговые автоматы	Savdo avtomatlari
385	drugoe-servisnoe-oborudovanie	Другое сервисное оборудование	servisnoe-oborudovanie-i-prinadlejnosti	2024-10-17 13:30:04	\N	0	Other service equipment	Другое сервисное оборудование	Boshqa xizmat ko'rsatish uskunalari
395	aksessuari-dlya-kabelnikh-sistem	Аксессуары для кабельных систем	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	1	Accessories for cable systems	Аксессуары для кабельных систем	Kabel tizimlari uchun aksessuarlar
396	batareyki-akkumulyatori	Батарейки, аккумуляторы	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	28	Batteries, accumulators	Батарейки, аккумуляторы	Batareyalar, akkumulyatorlar
397	generatori	Генераторы	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Generator	Генератор	Generator
398	istochniki-pitaniya	Источники питания	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Power supplies	Источники питания	Quvvat manbalari
399	pereklyuchateli	Переключатели	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	1	Switches	Переключатели	Kalitlar
400	provoda-kabeli-i-kabelnie-sborki	Провода, кабели и кабельные сборки	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Wires, cables and cable assemblies	Провода, кабели и кабельные сборки	Simlar, kabellar va kabel majmualari
401	produkti-solnechnoy-energii	Продукты солнечной энергии	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	4	Solar energy products	Продукты солнечной энергии	Quyosh energiyasi mahsulotlari
402	promishlennie-kontrolleri	Промышленные контроллеры	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Industrial controllers	Промышленные контроллеры	Sanoat nazoratchilari
403	professionalnoe-audio-video-i-osvetshenie	Профессиональное аудио, видео и освещение	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Professional audio, video and lighting	Профессиональное аудио, видео и освещение	Professional audio, video va yoritish
404	soediniteli-i-klemmi	Соединители и клеммы	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	1	Connectors and terminals	Соединители и клеммы	Ulagichlar va terminallar
405	elektricheskie-instrumenti	Электрические инструменты	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Power tools	Электрические инструменты	Elektr asboblari
406	elektrodvigateli	Электродвигатели	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Electric motors	Электродвигатели	Elektr dvigatellari
407	elektrotekhnicheskie-prinadlejnosti	Электротехнические принадлежности	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	0	Electrical accessories	Электротехнические принадлежности	Elektr aksessuarlar
408	prochie-elektrotekhnicheskie-oborudovaniya-i-materiali	Прочие электротехнические оборудования и материалы	elektrotekhnicheskoe-oborudovanie-i-materiali	2024-10-17 13:30:04	\N	12	Other electrical equipment and materials	Прочие электротехнические оборудования и материалы	Boshqa elektr jihozlari va materiallari
409	akusticheskie-sistemi-i-aksessuari	Акустические системы и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Acoustic systems and accessories	Акустические системы и аксессуары	Akustik tizimlar va aksessuarlar
410	blokcheyn-mayneri	Блокчейн майнеры	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Blockchain miners	Блокчейн майнеры	Blokcheyn konchilari
411	video-foto-i-aksessuari	Видео фото и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Video photo and accessories	Видео фото и аксессуары	Video foto va aksessuarlar
412	videoigri-i-aksessuari	Видеоигры и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Video Games and Accessories	Видеоигры и аксессуары	Video o'yinlar va aksessuarlar
413	domashnee-audio-video-i-aksessuari	Домашнее аудио, видео и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Home Audio, Video & Accessories	Домашнее аудио, видео и аксессуары	Uy audio, video va aksessuarlar
414	kompyuternie-komplektuyutshie-i-programmnoe-obespechenie	Компьютерные комплектующие и программное обеспечение	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Computer hardware and software	Компьютерные комплектующие и программное обеспечение	Kompyuter texnikasi va dasturiy ta'minoti
415	mobilnie-telefoni-i-aksessuari	Мобильные телефоны и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Mobile phones and accessories	Мобильные телефоны и аксессуары	Mobil telefonlar va aksessuarlar
416	naushniki	Наушники	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Headphones	Наушники	Naushniklar
1648	tovary-dlya-barbekyu	Товары для барбекю	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	0	BBQ Supplies	Товары для барбекю	Barbekyu uchun materiallar
417	portativnoe-audio-video-i-aksessuari	Портативное аудио, видео и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Portable Audio, Video & Accessories	Портативное аудио, видео и аксессуары	Portativ audio, video va aksessuarlar
418	prezentatsionnoe-oborudovanie	Презентационное оборудование	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Presentation equipment	Презентационное оборудование	Taqdimot uchun uskunalar
419	tv-priemniki-i-aksessuari	ТВ приемники и аксессуары	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	TV receivers and accessories	ТВ приемники и аксессуары	Televizor qabul qiluvchilar va aksessuarlar
420	umnaya-elektronika	Умная электроника	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Smart electronics	Умная электроника	Aqlli elektronika
421	elektronnie-publikatsii	Электронные публикации	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Electronic publications	Электронные публикации	Elektron nashrlar
422	elektronnie-sigareti	Электронные сигареты	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Electronic cigarettes	Электронные сигареты	Elektron sigaretalar
423	zapchasti-dlya-mobilnikh-telefonov	Запчасти для мобильных телефонов	bitovaya-elektronika	2024-10-17 13:30:04	\N	0	Spare parts for mobile phones	Запчасти для мобильных телефонов	Mobil telefonlar uchun ehtiyot qismlar
424	chasto-ispolzuemie-aksessuari-i-zapchasti	Часто используемые аксессуары и запчасти	bitovaya-elektronika	2024-10-17 13:30:04	\N	4	Frequently used accessories and spare parts	Часто используемые аксессуары и запчасти	Tez-tez ishlatiladigan aksessuarlar va ehtiyot qismlar
425	drugaya-bitovaya-elektronika	Другая бытовая электроника	bitovaya-elektronika	2024-10-17 13:30:04	\N	1	Other consumer electronics	Другая бытовая электроника	Boshqa maishiy elektronika
426	vodonagrevateli	Водонагреватели	bitovaya-tekhnika	2024-10-17 13:30:04	\N	5	Water heaters	Водонагреватели	Suv isitgichlari
427	dispenseri-vlajnikh-salfetok	Диспенсеры влажных салфеток	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Wet wipes dispensers	Диспенсеры влажных салфеток	Nam salfetkalar uchun dispenserlar
428	domashnie-obogrevateli	Домашние обогреватели	bitovaya-tekhnika	2024-10-17 13:30:04	\N	3	Home heaters	Домашние обогреватели	Uy isitgichlari
429	kukhonnaya-tekhnika	Кухонная техника	bitovaya-tekhnika	2024-10-17 13:30:04	\N	601	Kitchen appliances	Кухонная техника	Oshxona jihozlari
430	prachechnaya-tekhnika	Прачечная техника	bitovaya-tekhnika	2024-10-17 13:30:04	\N	5	Laundry equipment	Прачечная техника	Kir yuvish uskunalari
431	pribori-dlya-krasoti-i-lichnoy-gigieni	Приборы для красоты и личной гигиены	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Beauty and personal hygiene devices	Приборы для красоты и личной гигиены	Go'zallik va shaxsiy gigiena vositalari
432	pribori-dlya-ochistki-vodi	Приборы для очистки воды	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Water purification devices	Приборы для очистки воды	Suvni tozalash moslamalari
433	pribori-dlya-chistki-i-uborki	Приборы для чистки и уборки	bitovaya-tekhnika	2024-10-17 13:30:04	\N	13	Cleaning and tidying devices	Приборы для чистки и уборки	Tozalash va tozalash moslamalari
434	pribori-konditsionirovaniya-vozdukha	Приборы кондиционирования воздуха	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Air conditioning devices	Приборы кондиционирования воздуха	Konditsioner qurilmalar
435	skladskie-ostatki-bitovoy-tekhniki	Складские остатки бытовой техники	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Warehouse balances of household appliances	Складские остатки бытовой техники	Maishiy texnika omborlari balanslari
436	sushilki-dlya-ruk	Сушилки для рук	bitovaya-tekhnika	2024-10-17 13:30:04	\N	0	Hand dryers	Сушилки для рук	Qo'l quritgichlar
437	kholodilniki-i-morozilniki	Холодильники и морозильники	bitovaya-tekhnika	2024-10-17 13:30:04	\N	2	Refrigerators and freezers	Холодильники и морозильники	Muzlatgichlar va muzlatgichlar
438	zapchasti-bitovoy-tekhniki	Запчасти бытовой техники	bitovaya-tekhnika	2024-10-17 13:30:04	\N	1	Spare parts for household appliances	Запчасти бытовой техники	Maishiy texnika uchun ehtiyot qismlar
439	drugaya-bitovaya-tekhnika	Другая бытовая техника	bitovaya-tekhnika	2024-10-17 13:30:04	\N	3	Other household appliances	Другая бытовая техника	Boshqa maishiy texnika
478	avariynaya-signalizatsiya	Аварийная сигнализация	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Alarm	Аварийная сигнализация	Signal
479	bezopasnost-dorojnogo-dvijeniya	Безопасность дорожного движения	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	2	Road safety	Безопасность дорожного движения	Yo'l harakati xavfsizligi
480	videonablyudenie	Видеонаблюдение	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Video surveillance	Видеонаблюдение	Video kuzatuv
481	zamki-i-klyuchi	Замки и ключи	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Locks and keys	Замки и ключи	Qulflar va kalitlar
482	okhrannie-uslugi	Охранные услуги	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	1	Security services	Охранные услуги	Xavfsizlik xizmatlari
483	politseyskie-i-voennie-prinadlejnosti	Правоохранительные и военные принадлежности	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Law enforcement and military supplies	Правоохранительные и военные принадлежности	Huquqni muhofaza qilish va harbiy ta'minot
484	protivopojarnie-prinadlejnosti	Противопожарные принадлежности	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Firefighting accessories	Противопожарные принадлежности	Yong'inga qarshi aksessuarlar
485	seyfi	Сейфы	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Safes	Сейфы	Seyflar
486	sistemi-kontrolya-dostupa	Системы контроля доступа	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Access control systems	Системы контроля доступа	Kirish nazorati tizimlari
487	sredstva-individualnoy-zatshiti	Средства индивидуальной защиты	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Personal protective equipment	Средства индивидуальной защиты	Shaxsiy himoya vositalari
488	tovari-dlya-bezopasnosti-na-vode	Товары для безопасности на воде	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Water Safety Products	Товары для безопасности на воде	Suv xavfsizligi mahsulotlari
489	tovari-dlya-samooboroni	Товары для самообороны	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	0	Self Defense Products	Товары для самообороны	O'zini himoya qilish mahsulotlari
490	drugie-produkti-bezopasnosti-i-zatshiti	Другие продукты безопасности и защиты	bezopasnost-i-zatshita	2024-10-17 13:30:05	\N	1	Other safety and security products	Другие продукты безопасности и защиты	Boshqa xavfsizlik va xavfsizlik mahsulotlari
560	abrazivnie-materiali	Абразивные материалы	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Abrasive materials	Абразивные материалы	Abraziv materiallar
561	gidravlicheskie-instrumenti	Гидравлические инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Hydraulic tools	Гидравлические инструменты	Gidravlik asboblar
562	izmeritelnie-instrumenti1	Измерительные инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	1	Measuring instruments	Измерительные инструменты	O'lchov asboblari
563	izmeritelnie-pribori	Измерительные приборы	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Measuring instruments	Измерительные приборы	O'lchov asboblari
564	ispitatelnoe-oborudovanie	Испытательное оборудование	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Test equipment	Испытательное оборудование	Sinov uskunalari
565	klapani	Клапаны	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	2	Valve	Клапан	Vana
566	krepyojnie-detali	Крепёжные детали	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	13	Fasteners	Крепёжные детали	Mahkamlagichlar
567	mashinnaya-obrabotka	Машинная обработка	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Machine processing	Машинная обработка	Mashinada ishlov berish
568	metiallicheskie-izdeliya	Металлические изделия	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	3	Hardware	Металлические изделия	Uskuna
569	nasosi-i-ikh-chasti	Насосы и их части	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Pumps and their parts	Насосы и их части	Nasoslar va ularning qismlari
570	pnevmaticheskie-instrumenti	Пневматические инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Pneumatic tools	Пневматические инструменты	Pnevmatik asboblar
571	pogruzochno-razgruzochnie-instrumenti	Погрузочно-разгрузочные инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Loading and unloading tools	Погрузочно-разгрузочные инструменты	Yuklash va tushirish asboblari
572	prinadlejnosti-dlya-svarki-i-payki	Принадлежности для сварки и пайки	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	1	Welding and Soldering Accessories	Принадлежности для сварки и пайки	Payvandlash va lehimlash uchun aksessuarlar
573	ruchnie-instrumenti	Ручные инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Hand tools	Ручные инструменты	Qo'l asboblari
574	svyorla	Свёрла	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Drill	Сверлить	Matkap
575	fitingi-dlya-trub	Фитинги для труб	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	2	Pipe fittings	Фитинги для труб	Quvur qismlari
576	khranenie-instrumentov	Хранение инструментов	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Storage of tools	Хранение инструментов	Asboblarni saqlash
577	tsepi	Цепи	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Chains	Цепи	Zanjirlar
578	elektricheskie-i-mekhanichechkie-instrumenti	Электрические и механические инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	1	Electrical and mechanical tools	Электрические и механические инструменты	Elektr va mexanik asboblar
579	aksessuari-dlya-elektroinstrumentov	Аксессуары для электроинструментов	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Accessories for power tools	Аксессуары для электроинструментов	Elektr asboblari uchun aksessuarlar
580	chasti-instrumentov	Части инструментов	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	0	Parts of tools	Части инструментов	Asboblarning qismlari
581	drugie-instrumenti	Другие инструменты	instrumenti-i-oborudovaniya	2024-10-17 13:30:06	\N	7	Other tools	Другие инструменты	Boshqa vositalar
658	biogaz	Биогаз	energiya	2024-10-17 13:30:06	\N	0	Biogas	Биогаз	Biogaz
659	biodizel	Биодизель	energiya	2024-10-17 13:30:06	\N	0	Biodiesel	Биодизель	Biodizel
660	drevesniy-ugol	Древесный уголь	energiya	2024-10-17 13:30:06	\N	0	Charcoal	Древесный уголь	Ko'mir
661	iskopaemiy-ugol	Ископаемый уголь	energiya	2024-10-17 13:30:06	\N	0	Fossil coal	Ископаемый уголь	Qazib olinadigan ko'mir
662	kamennougolniy-koks	Каменноугольный кокс	energiya	2024-10-17 13:30:06	\N	0	Coal coke	Каменноугольный кокс	Ko'mir koks
663	koksoviy-gaz	Коксовый газ	energiya	2024-10-17 13:30:06	\N	0	Coke oven gas	Коксовый газ	Koks gazi
664	nefteprodukti	Нефтепродукты	energiya	2024-10-17 13:30:06	\N	1	Petroleum products	Нефтепродукты	Neft mahsulotlari
665	neft	Нефть	energiya	2024-10-17 13:30:06	\N	0	Oil	Масло	Yog '
666	prirodniy-gaz	Природный газ	energiya	2024-10-17 13:30:06	\N	0	Natural gas	Природный газ	Tabiiy gaz
667	promishlennoe-toplivo	Промышленное топливо	energiya	2024-10-17 13:30:06	\N	0	Industrial fuel	Промышленное топливо	Sanoat yoqilg'isi
668	toplivnie-granuli	Топливные гранулы	energiya	2024-10-17 13:30:06	\N	0	Fuel pellets	Топливные гранулы	Yoqilg'i granulalari
669	drugie-produkti-svyazannie-s-energiey	Другие продукты, связанные с энергией	energiya	2024-10-17 13:30:06	\N	1	Other energy related products	Другие продукты, связанные с энергией	Boshqa energiya bilan bog'liq mahsulotlar
670	bobovie	Бобовые	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Legumes	Бобовые	Dukkaklilar
671	vanilnie-bobi	Ванильные бобы	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Vanilla beans	Ванильные бобы	Vanilla loviya
672	gribi-i-tryufeli	Грибы и трюфели	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Mushrooms and truffles	Грибы и трюфели	Qo'ziqorinlar va truffles
673	dekorativnie-rasteniya	Декоративные растения	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Ornamental plants	Декоративные растения	Manzarali o'simliklar
674	jivotnovodstvo-i-ptitsevodstvo	Животноводство и птицеводство	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	1	Livestock and poultry farming	Животноводство и птицеводство	Chorvachilik va parrandachilik
675	jivotnie-i-rastitelnie-masla	Животные и растительные масла	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Animal and vegetable oils	Животные и растительные масла	Hayvon va o'simlik moylari
676	zerna	Зерна	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Grains	Зерна	Donlar
677	kakao-bobi	Какао-бобы	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Cocoa beans	Какао-бобы	Kakao loviyalari
678	korm-dlya-jivotnikh	Корм для животных	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Pet food	Корм для животных	Uy hayvonlari uchun oziq-ovqat
679	kofeynie-zyorna	Кофейные зёрна	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Coffee beans	Кофейные зёрна	Qahva donalari
680	lesomateriali	Сельское хозяйство	selskoe-khozyaystvo	2024-10-17 17:48:48	2025-06-06 11:25:21	1	Timber	Лесоматериалы	Yog'och
681	orekhi-i-kostochki	Сельское хозяйство	selskoe-khozyaystvo	2024-10-17 17:48:48	2025-06-06 11:25:28	1	Nuts and pits	Орехи и косточки	Yong'oq va chuqurchalar
682	svejie-ovotshi1	Свежие овощи	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Fresh vegetables	Свежие овощи	Yangi sabzavotlar
683	svejie-frukti	Свежие фрукты	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Fresh fruit	Свежие фрукты	Yangi meva
684	selkhoz-oborudovanie	Сельхоз оборудование	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Agricultural equipment	Сельхоз оборудование	Qishloq xo'jaligi uskunalari
685	selkhoz-otkhodi	Сельхоз отходы	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Agricultural waste	Сельхоз отходы	Qishloq xo'jaligi chiqindilari
686	semena-i-lukovitsi-rasteniy	Семена и луковицы растений	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	1	Seeds and bulbs of plants	Семена и луковицы растений	O'simliklarning urug'lari va piyozlari
687	drugie-selkhoz-produkti	Другие сельхоз продукты	selskoe-khozyaystvo	2024-10-17 17:48:48	\N	0	Other agricultural products	Другие сельхоз продукты	Boshqa qishloq xo'jaligi mahsulotlari
816	arkhitekturnie-oblomi	Архитектурные обломы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Architectural failures	Архитектурные обломы	Arxitekturadagi muvaffaqiyatsizliklar
817	balyasini-i-perila	Балясины и перила	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Balusters and railings	Балясины и перила	Balusterlar va panjaralar
818	vannaya-i-kukhnya	Ванная и кухня	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	3	Bathroom and kitchen	Ванная и кухня	Hammom va oshxona
819	gidroizolyatsionnie-materiali	Гидроизоляционные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	7	Waterproofing materials	Гидроизоляционные материалы	Gidroizolyatsiya materiallari
820	dver-okno-i-aksessuari	Дверь, Окно и Аксессуары	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Door, Window and Accessories	Дверь, Окно и Аксессуары	Eshik, oyna va aksessuarlar
821	dekorativnie-plenki	Декоративные пленки	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	1	Decorative films	Декоративные пленки	Dekorativ filmlar
822	jelezobetonnie-izdeliya-jbi	Железобетонные изделия ЖБИ	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	15	Reinforced concrete products RC products	Железобетонные изделия ЖБИ	Temir-beton buyumlar RC mahsulotlari
823	zatshitnie-ugolniki-dlya-sten	Защитные угольники для стен	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	6	Protective corners for walls	Защитные угольники для стен	Devor uchun himoya burchaklar
824	zvukoizolyatsionnie-materiali	Звукоизоляционные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Soundproofing materials	Звукоизоляционные материалы	Ovoz o'tkazmaydigan materiallar
825	zimnie-sadi-i-teplitsi	Зимние сады и теплицы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Winter gardens and greenhouses	Зимние сады и теплицы	Qishki bog'lar va issiqxonalar
826	kamen	Камень и каменная плитка	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	2025-03-12 11:08:19	102	Stone and stone tiles	Камень и каменная плитка	Tosh va tosh plitkalar
827	kamini-pechi	Камины, Печи	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Fireplaces, Stoves	Камины, Печи	Kaminlar, pechkalar
828	keramicheskie-plitki-i-aksessuari	Керамические плитки и аксессуары	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	3	Ceramic tiles and accessories	Керамические плитки и аксессуары	Seramika plitalari va aksessuarlar
829	kladochnie-materiali	Кладочные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	57	Masonry materials	Кладочные материалы	Duvarcılık materiallari
830	lestnitsi-i-ikh-chasti	Лестницы и их части	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Stairs and their parts	Лестницы и их части	Zinapoya va ularning qismlari
831	lifti-i-eskalatori	Лифты и эскалаторы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Elevators and escalators	Лифты и эскалаторы	Liftlar va eskalatorlar
1646	instrumenty-i-aksessuary	Инструменты и аксессуары	apparatnoe-obespechenie	2025-01-20 04:35:19	\N	283	Tools and accessories	Инструменты и аксессуары	Asboblar va aksessuarlar
832	metallicheskie-stroitelnie-materiali	Металлические строительные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	11	Metal building materials	Металлические строительные материалы	Metall qurilish materiallari
833	mnogofunktsionalnie-materiali	Многофункциональные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Multifunctional materials	Многофункциональные материалы	Ko'p funktsiyali materiallar
834	mozaika	Мозаика	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Mosaic	Мозаика	Mozaika
835	napolnie-pokritiya-i-aksessuari	Напольные покрытия и аксессуары	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	45	Floor coverings and accessories	Напольные покрытия и аксессуары	Zamin qoplamalari va aksessuarlar
836	napolnie-sistemi-otopleniya-i-ikh-chasti	Напольные системы отопления и их части	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Underfloor heating systems and their parts	Напольные системы отопления и их части	Yerdan isitish tizimlari va ularning qismlari
837	nedvijimost	Недвижимость	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Real estate	Недвижимость	Ko'chmas mulk
838	oboi-pokritie-sten	Обои, покрытие стен	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	6	Wallpaper, wall covering	Обои, покрытие стен	Fon rasmi, devor qoplamasi
839	ognezatshitnie-materiali	Огнезащитные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Fire protection materials	Огнезащитные материалы	Yong'inga qarshi materiallar
840	opalubka	Опалубка	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	7	Decking	Опалубка	Decking
841	pilomateriali	Пиломатериалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	1	Lumber	Пиломатериалы	Yog'och
842	plastikovie-stroitelnie-materiali	Пластиковые строительные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Plastic building materials	Пластиковые строительные материалы	Plastik qurilish materiallari
843	potolki	Потолки	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Ceilings	Потолки	Shiftlar
844	produkti-dlya-zemlyanikh-rabot	Продукты для земляных работ	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Products for earthworks	Продукты для земляных работ	Tuproq ishlari uchun mahsulotlar
845	svetoprozrachnie-fasadi-i-aksessuari	Светопрозрачные фасады и аксессуары	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	7	Translucent facades and accessories	Светопрозрачные фасады и аксессуары	Shaffof jabhalar va aksessuarlar
846	sistemi-otopleniya-ventilyatsii-i-konditsionirovaniya	Системы отопления, вентиляции и кондиционирования	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	367	Heating, ventilation and air conditioning systems	Системы отопления, вентиляции и кондиционирования	Isitish, shamollatish va havoni tozalash tizimlari
847	stoleshnitsi	Столешницы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	0	Countertops	Столешницы	Stol usti
848	stroitelnoe-steklo	Строительное стекло	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	1	Construction glass	Строительное стекло	Qurilish oynasi
849	stroitelnie-lesa-i-stremyanki	Строительные леса и стремянки	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	1	Scaffolding and stepladders	Строительные леса и стремянки	Iskala va zinapoyalar
850	stroitelnie-pliti	Строительные плиты	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	2	Construction slabs	Строительные плиты	Qurilish plitalari
851	teploizolyatsionnie-materiali	Теплоизоляционные материалы	stroitelstvo-i-nedvijimost	2024-10-17 17:48:53	\N	3	Thermal insulation materials	Теплоизоляционные материалы	Issiqlik izolyatsiyalash materiallari
852	prochee-stroitelstvo-i-nedvijimost	Прочее строительство и недвижимость	stroitelstvo-i-nedvijimost	2024-10-17 17:48:54	\N	10	Other construction and real estate	Прочее строительство и недвижимость	Boshqa qurilish va ko'chmas mulk
1634	avtoelektronika	Автоэлектроника	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	51	Automotive electronics	Автоэлектроника	Avtomobil elektronikasi
1635	apparatnye-sredstva	Аппаратные средства	apparatnoe-obespechenie	2025-01-20 04:35:19	\N	1545	Hardware	Аппаратные средства	Uskuna
1637	osveshchenie-i-elektrika	Освещение и электрика	apparatnoe-obespechenie	2025-01-20 04:35:19	\N	867	Lighting and electrical	Освещение и электрика	Yoritish va elektr
1638	santekhnika-i-santekhnika	Сантехника и сантехника	apparatnoe-obespechenie	2025-01-20 04:35:19	\N	210	Plumbing and sanitary ware	Сантехника и сантехника	Santexnika va sanitariya-texnik vositalar
1639	avtomobilnye-aksessuary	Автомобильные аксессуары	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	51	Car accessories	Автомобильные аксессуары	Avtomobil aksessuarlari
1640	avtomobilnye-instrumenty-i-oborudovanie	Автомобильные инструменты и оборудование	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	183	Automotive tools and equipment	Автомобильные инструменты и оборудование	Avtomobil asboblari va jihozlari
1641	avtobezopasnost-i-zashchita	Автобезопасность и защита	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	14	Automotive Safety and Security	Автобезопасность и защита	Avtomobil xavfsizligi va xavfsizligi
1642	avtomobilnye-detali	Автомобильные детали	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	236	Automotive parts	Автомобильные детали	Avtomobil qismlari
1643	transportnye-sredstva	Транспортные средства	avtomobili-i-aksessuary	2025-01-20 04:35:19	\N	13	Vehicles	Транспортные средства	Avtomobillar
1644	postavki-napitkov	Поставки напитков	eda-i-napitki	2025-01-20 04:35:19	\N	128	Beverage supplies	Поставки напитков	Ichimlik buyumlari
1645	prodovolstvennye-tovary	Продовольственные товары	eda-i-napitki	2025-01-20 04:35:19	\N	1376	Grocery products	Продовольственные товары	Oziq-ovqat mahsulotlari
1649	odnorazovaya-posuda	Одноразовая посуда	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	0	Disposable tableware	Одноразовая посуда	Bir martali ishlatiladigan idishlar
1650	posuda-dlya-pitya	Посуда для питья	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	0	Drinking utensils	Посуда для питья	Ichimlik idishlari
1652	eda-i-napitki	Еда и напитки	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	36	Egg products	Яичные продукты	Tuxum mahsulotlari
1653	gostinichnye-prinadlezhnosti	Гостиничные принадлежности	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	27	Hotel accessories	Гостиничные принадлежности	Mehmonxona aksessuarlari
1654	obshchee-oborudovanie	Общее оборудование	apparatnoe-obespechenie	2025-01-20 04:35:19	\N	609	General equipment	Общее оборудование	Umumiy jihozlar
1655	khranenie-i-organayzery-dlya-doma	Хранение и органайзеры для дома	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	4	Storage and Organizers for Home	Хранение и органайзеры для дома	Uy uchun saqlash va tashkilotchilar
1656	khozyaystvennye-tovary	Хозяйственные товары	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	239	Household goods	Хозяйственные товары	Uy-ro'zg'or buyumlari
1657	kukhonnye-prinadlezhnosti	Кухонные принадлежности	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	5	Kitchenware	Кухонные принадлежности	Oshxona anjomlari
1658	tovary-dlya-zhivotnykh	Товары для животных	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	48	Pet Products	Товары для животных	Uy hayvonlari uchun mahsulotlar
1659	stolovaya-posuda	Столовая посуда	domashnie-i-domashnie-zhivotnye	2025-01-20 04:35:19	\N	4	Tableware	Столовая посуда	Idish idishlari
1660	tovary-dlya-tvorchestva-i-rukodeliya	Товары для творчества и рукоделия	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-20 04:35:19	\N	83	Goods for creativity and handicrafts	Товары для творчества и рукоделия	Ijodkorlik va hunarmandchilik uchun mahsulotlar
1661	ofisnaya-elektronika	Офисная техника	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-20 04:35:19	2025-01-21 12:04:01	57	Office equipment	Офисная техника	Ofis jihozlari
1662	raskhodnye-materialy-dlya-printerov	Расходные материалы для принтеров и кассовых аппаратов	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-20 04:35:19	2025-01-21 12:05:04	14	Consumables for printers and cash registers	Расходные материалы для принтеров и кассовых аппаратов	Printerlar va kassa apparatlari uchun sarf materiallari
1663	velosipedy-i-rolikovye-konki	Велосипеды и роликовые коньки	sport-i-otdykh-na-prirode	2025-01-20 04:35:19	\N	0	Bicycles and roller skates	Велосипеды и роликовые коньки	Velosipedlar va konkilar
1664	kemping-i-otdykh	Кемпинг и отдых	sport-i-otdykh-na-prirode	2025-01-20 04:35:19	\N	167	Camping and recreation	Кемпинг и отдых	Lager va dam olish
1665	fitnes-oborudovanie-i-aksessuary	Фитнес-оборудование и аксессуары	sport-i-otdykh-na-prirode	2025-01-20 04:35:19	\N	39	Fitness Equipment and Accessories	Фитнес-оборудование и аксессуары	Fitness jihozlari va aksessuarlari
1666	sportivnye-tovary	Спортивные товары	sport-i-otdykh-na-prirode	2025-01-20 04:35:19	\N	170	Sporting goods	Спортивные товары	Sport buyumlari
1667	inventar-dlya-vodnykh-vidov-sporta	Инвентарь для водных видов спорта	sport-i-otdykh-na-prirode	2025-01-20 04:35:19	\N	52	Water sports equipment	Инвентарь для водных видов спорта	Suv sporti jihozlari
1668	kantselyarskie-prinadlezhnosti	Канцелярские принадлежности	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-20 04:35:19	\N	543	Stationery	Канцелярские принадлежности	Kantselyariya tovarlari
1669	makiyazh-i-kosmetika	Макияж и косметика	krasota-i-lichnaya-gigiena	2025-01-20 04:35:19	\N	48	Makeup and cosmetics	Макияж и косметика	Makiyaj va kosmetika
1670	kosmeticheskie-protsedury-i-prinadlezhnosti-dlya-salonov-krasoty	Косметические процедуры и принадлежности для салонов красоты	krasota-i-lichnaya-gigiena	2025-01-20 04:35:19	\N	41	Cosmetic procedures and accessories for beauty salons	Косметические процедуры и принадлежности для салонов красоты	Go'zallik salonlari uchun kosmetik muolajalar va aksessuarlar
1671	sredstva-lichnoy-gigieny-i-uborka-doma	Средства личной гигиены и уборка дома	krasota-i-lichnaya-gigiena	2025-01-20 04:35:19	\N	55	Personal hygiene and home cleaning	Средства личной гигиены и уборка дома	Shaxsiy gigiena va uyni tozalash
1672	sumki-i-bagazh	Сумки и багаж	modnye-aksessuary-i-obuv	2025-01-20 04:35:19	\N	58	Bags and luggage	Сумки и багаж	Bagaj va sumkalar
1674	yuvelirnye-izdeliya-i-chasy	Ювелирные изделия и часы	modnye-aksessuary-i-obuv	2025-01-20 04:35:19	\N	29	Jewelry and watches	Ювелирные изделия и часы	Zargarlik buyumlari va soatlar
1675	muzhskaya-i-zhenskaya-obuv	Мужская и женская обувь	modnye-aksessuary-i-obuv	2025-01-20 04:35:19	\N	95	Men's and women's shoes	Мужская и женская обувь	Erkaklar va ayollar poyafzallari
1676	sharfy,-shali-i-perchatki	Шарфы, шали и перчатки	modnye-aksessuary-i-obuv	2025-01-20 04:35:19	\N	6	Scarves, shawls and gloves	Шарфы, шали и перчатки	Sharflar, sharflar va qo'lqoplar
1677	modnye-aksessuary-dlya-muzhchin-i-zhenshchin	Модные аксессуары для мужчин и женщин	modnye-aksessuary-i-obuv	2025-01-20 04:35:19	\N	161	Fashion accessories for men and women	Модные аксессуары для мужчин и женщин	Erkaklar va ayollar uchun moda aksessuarlari
1678	dekorativnye-aktsenty	Декоративные акценты	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	593	Decorative accents	Декоративные акценты	Dekorativ aksanlar
1679	aromaty-i-aromatizatory-dlya-doma	Ароматы и ароматизаторы для дома	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	2	Fragrances and home fragrances	Ароматы и ароматизаторы для дома	Xushbo'y hidlar va uy hidlari
1680	mebel-dlya-doma-i-sada	Мебель для дома и сада	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	497	Furniture for home and garden	Мебель для дома и сада	Uy va bog' uchun mebel
1681	domashniy-tekstil-i-postelnye-prinadlezhnosti	Домашний текстиль и постельные принадлежности	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	129	Home textiles and bedding	Домашний текстиль и постельные принадлежности	Uy to'qimachilik va choyshablar
1682	ofisnaya-i-kommercheskaya-mebel	Офисная и коммерческая мебель	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	134	Office and commercial furniture	Офисная и коммерческая мебель	Ofis va tijorat mebellari
1683	kaminy-i-aksessuary	Камины и аксессуары	mebel-i-domashniy-dekor	2025-01-20 04:35:19	\N	3	Fireplaces and accessories	Камины и аксессуары	Kamin va aksessuarlar
1684	zdorove-i-blagopoluchie	Здоровье и благополучие	zdravookhranenie	2025-01-20 04:35:19	\N	182	Health and Wellbeing	Здоровье и благополучие	Salomatlik va farovonlik
1685	oborudovanie-i-prinadlezhnosti-dlya-stomatologii	Оборудование и принадлежности для стоматологии	zdravookhranenie	2025-01-20 04:35:19	\N	13	Equipment and accessories for dentistry	Оборудование и принадлежности для стоматологии	Stomatologiya uchun asbob-uskunalar va aksessuarlar
1686	prinadlezhnosti-dlya-bolnits-i-otdeleniy	Принадлежности для больниц и отделений	zdravookhranenie	2025-01-20 04:35:19	\N	125	Hospital and Department Supplies	Принадлежности для больниц и отделений	Kasalxona va bo'lim materiallari
1687	meditsina-i-zdravookhranenie	Медицина и здравоохранение	zdravookhranenie	2025-01-20 04:35:19	\N	302	Medicine and Healthcare	Медицина и здравоохранение	Tibbiyot va sog'liqni saqlash
1688	operatsionnye-i-khirurgicheskie-prinadlezhnosti	Операционные и хирургические принадлежности	zdravookhranenie	2025-01-20 04:35:19	\N	51	Operating and surgical supplies	Операционные и хирургические принадлежности	Operatsion va jarrohlik jihozlari
1689	tovary-dlya-reabilitatsii-i-fizioterapii	Товары для реабилитации и физиотерапии	zdravookhranenie	2025-01-20 04:35:19	\N	12	Products for rehabilitation and physical therapy	Товары для реабилитации и физиотерапии	Reabilitatsiya va fizioterapiya uchun mahsulotlar
1690	ar/vr	AR/VR	mobilnaya-elektronika	2025-01-20 04:35:19	\N	0	AR/VR	AR/VR	AR/VR
1691	svyaz-i-telekommunikatsii	Связь и телекоммуникации	mobilnaya-elektronika	2025-01-20 04:35:19	\N	23	Communications and Telecommunications	Связь и телекоммуникации	Aloqa va telekommunikatsiyalar
1692	mobilnye-ustroystva	Мобильные устройства	mobilnaya-elektronika	2025-01-20 04:35:19	\N	335	Mobile devices	Мобильные устройства	Mobil qurilmalar
1693	aksessuary-i-detali-dlya-mobilnykh-telefonov	Аксессуары и детали для мобильных телефонов	mobilnaya-elektronika	2025-01-20 04:35:19	\N	185	Accessories and parts for mobile phones	Аксессуары и детали для мобильных телефонов	Mobil telefonlar uchun aksessuarlar va ehtiyot qismlar
1694	aksessuary-i-detali-dlya-planshetov	Аксессуары и детали для планшетов	mobilnaya-elektronika	2025-01-20 04:35:19	\N	6	Accessories and parts for tablets	Аксессуары и детали для планшетов	Planshetlar uchun aksessuarlar va ehtiyot qismlar
1695	aksessuary-dlya-mp3-pleerov	Аксессуары для MP3-плееров	mobilnaya-elektronika	2025-01-20 04:35:19	\N	55	Accessories for MP3 players	Аксессуары для MP3-плееров	MP3 pleerlar uchun aksessuarlar
1696	nosimye-elektronnye-ustroystva	Носимые электронные устройства	mobilnaya-elektronika	2025-01-20 04:35:19	\N	5	Wearable electronic devices	Носимые электронные устройства	Kiyiladigan elektron qurilmalar
1699	produkty-kontrolya-dostupa	Продукты контроля доступа	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	0	Access Control Products	Продукты контроля доступа	Kirish nazorati mahsulotlari
1700	upravlenie-energopotrebleniem	Управление энергопотреблением	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	540	Energy management	Управление энергопотреблением	Energiya boshqaruvi
1701	tsifrovaya-vyveska	Цифровая вывеска	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	1	Digital signage	Цифровая вывеска	Raqamli belgilar
1702	finansovoe-oborudovanie	Финансовое оборудование	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	0	Financial equipment	Финансовое оборудование	Moliyaviy uskunalar
1703	sistemy-bezopasnosti-obektov-i-upravleniya	Системы безопасности объектов и управления	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	3	Security systems of objects and management	Системы безопасности объектов и управления	Ob'ektlar va boshqaruvning xavfsizlik tizimlari
1704	svetodiodnoe-i-energosberegayushchee-osveshchenie	Светодиодное и энергосберегающее освещение	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	181	LED and energy saving lighting	Светодиодное и энергосберегающее освещение	LED va energiya tejovchi yoritish
1705	sistemy-predotvrashcheniya-poter-i-bezopasnosti-v-roznichnoy-torgovle	Системы предотвращения потерь и безопасности в розничной торговле	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	0	Loss Prevention and Security Systems in Retail	Системы предотвращения потерь и безопасности в розничной торговле	Chakana savdoda yo'qotishlarning oldini olish va xavfsizlik tizimlari
1706	robototekhnika	Робототехника	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	5	Robotics	Робототехника	Robototexnika
1707	sistemy-bezopasnosti-i-avariynoy-signalizatsii	Системы безопасности и аварийной сигнализации	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	84	Security and alarm systems	Системы безопасности и аварийной сигнализации	Xavfsizlik va signalizatsiya tizimlari
1708	kamery-bezopasnosti-i-produkty-videonablyudeniya	Камеры безопасности и продукты видеонаблюдения	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	4	Security Cameras and CCTV Products	Камеры безопасности и продукты видеонаблюдения	Xavfsizlik kameralari va CCTV mahsulotlari
1709	bezopasnost-umnogo-doma	Безопасность умного дома	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	271	Smart Home Security	Безопасность умного дома	Smart uy xavfsizligi
1710	stsenicheskoe-i-zvukovoe-oborudovanie	Сценическое и звуковое оборудование	umnaya-bytovaya-elektronika	2025-01-20 04:35:19	\N	3	Stage and sound equipment	Сценическое и звуковое оборудование	Sahna va ovoz uskunalari
1723	korporativnye-i-reklamnye-podarki	Корпоративные и рекламные подарки	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	10	Corporate and promotional gifts	Корпоративные и рекламные подарки	Korporativ va reklama sovg'alari
1724	podarki-na-prazdniki-i-po-sluchayu	Подарки на праздники и по случаю	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	62	Gifts for holidays and occasions	Подарки на праздники и по случаю	Bayramlar va bayramlar uchun sovg'alar
1725	igry-i-khobbi	Игры и хобби	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	70	Games and hobbies	Игры и хобби	O'yinlar va sevimli mashg'ulotlar
1726	prazdnichnye-ukrasheniya-i-prinadlezhnosti-dlya-vecherinok	Праздничные украшения и принадлежности для вечеринок	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	24	Festive Decorations and Party Supplies	Праздничные украшения и принадлежности для вечеринок	Bayram bezaklari va ziyofat materiallari
1727	podarki-i-aksessuary-dlya-kureniya	Подарки и аксессуары для курения	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	3	Gifts and smoking accessories	Подарки и аксессуары для курения	Sovg'alar va chekish uchun aksessuarlar
1728	tekhnicheskie-podarki-i-gadzhety	Технические подарки и гаджеты	podarki,-festivali-i-khobbi	2025-01-20 04:35:19	\N	8	Tech gifts and gadgets	Технические подарки и гаджеты	Texnik sovg'alar va gadjetlar
1732	oborudovanie-dlya-proizvodstva-odezhdy-i-tekstilya	Оборудование для производства одежды и текстиля	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	156	Equipment for the production of clothing and textiles	Оборудование для производства одежды и текстиля	Kiyim va to'qimachilik mahsulotlarini ishlab chiqarish uchun uskunalar
1733	oborudovanie-dlya-proizvodstva-elektroniki	Оборудование для производства электроники	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	35	Equipment for electronics manufacturing	Оборудование для производства электроники	Elektron ishlab chiqarish uchun uskunalar
1734	ekologicheskoe-oborudovanie	Экологическое оборудование	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	293	Ecological equipment	Экологическое оборудование	Ekologik uskunalar
1735	mashiny	Машины	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	231	Car	Машина	Avtomobil
1736	proizvodstvennoe-oborudovanie	Производственное оборудование	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	2084	Production equipment	Производственное оборудование	Ishlab chiqarish uskunalari
1737	servisnoe-oborudovanie	Сервисное оборудование	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	886	Service equipment	Сервисное оборудование	Xizmat ko'rsatish uskunalari
1738	upakovka-dlya-produktov-pitaniya-i-napitkov	Упаковка для продуктов питания и напитков	pechat-i-upakovka	2025-01-20 04:35:19	\N	55	Packaging for food and beverages	Упаковка для продуктов питания и напитков	Oziq-ovqat va ichimliklar uchun qadoqlash
1739	podarochnaya-i-roznichnaya-upakovka	Подарочная и розничная упаковка	pechat-i-upakovka	2025-01-20 04:35:19	\N	45	Gift and retail packaging	Подарочная и розничная упаковка	Sovg'a va chakana qadoqlash
1740	mashinostroenie-i-stroitelnaya-tekhnika	Машиностроение и строительная техника	mashiny-i-oborudovanie	2025-01-20 04:35:19	\N	1662	Mechanical engineering and construction equipment	Машиностроение и строительная техника	Mashinasozlik va qurilish uskunalari
1741	logisticheskaya-upakovka	Логистическая упаковка	pechat-i-upakovka	2025-01-20 04:35:19	\N	24	Logistics packaging	Логистическая упаковка	Logistik qadoqlash
1742	drugaya-upakovka-i-poligraficheskaya-produktsiya	Другая упаковка и полиграфическая продукция	pechat-i-upakovka	2025-01-20 04:35:19	\N	372	Other packaging and printing products	Другая упаковка и полиграфическая продукция	Boshqa qadoqlash va bosma mahsulotlar
1743	vspomogatelnye-materialy-dlya-upakovki	Вспомогательные материалы для упаковки	pechat-i-upakovka	2025-01-20 04:35:19	\N	36	Auxiliary materials for packaging	Вспомогательные материалы для упаковки	Qadoqlash uchun yordamchi materiallar
1744	upakovochnoe-i-poligraficheskoe-syre	Упаковочное и полиграфическое сырье	pechat-i-upakovka	2025-01-20 04:35:19	\N	34	Packaging and printing raw materials	Упаковочное и полиграфическое сырье	Qadoqlash va matbaa xomashyosi
1745	aktivnye-komponenty	Активные компоненты	elektronnye-komponenty	2025-01-20 04:35:19	\N	160	Active ingredients	Активные компоненты	Faol moddalar
1746	elektromekhanicheskie-komponenty	Электромеханические компоненты	elektronnye-komponenty	2025-01-20 04:35:19	\N	5299	Electromechanical components	Электромеханические компоненты	Elektromexanik komponentlar
1747	oborudovanie-i-prinadlezhnosti-dlya-elektronnogo-proizvodstva	Оборудование и принадлежности для электронного производства	elektronnye-komponenty	2025-01-20 04:35:19	\N	1912	Equipment and supplies for electronic production	Оборудование и принадлежности для электронного производства	Elektron ishlab chiqarish uchun uskunalar va jihozlar
1748	batarei-i-bloki-pitaniya	Батареи и блоки питания	elektronnye-komponenty	2025-01-20 04:35:19	\N	79	Batteries and power supplies	Батареи и блоки питания	Batareyalar va quvvat manbalari
1749	svetodiody-i-optoelektronika	Светодиоды и оптоэлектроника	elektronnye-komponenty	2025-01-20 04:35:19	\N	38	LEDs and Optoelectronics	Светодиоды и оптоэлектроника	LEDlar va optoelektronika
1750	passivnye-komponenty	Пассивные компоненты	elektronnye-komponenty	2025-01-20 04:35:19	\N	754	Passive components	Пассивные компоненты	Passiv komponentlar
1751	pp-i-proizvodstvennye-prinadlezhnosti	ПП и производственные принадлежности	elektronnye-komponenty	2025-01-20 04:35:19	\N	130	PP and production accessories	ПП и производственные принадлежности	PP va ishlab chiqarish aksessuarlari
1752	dekorativnye-i-upakovochnye-materialy-dlya-odezhdy	Декоративные и упаковочные материалы для одежды	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	2	Decorative and packaging materials for clothes	Декоративные и упаковочные материалы для одежды	Kiyimlar uchun dekorativ va qadoqlash materiallari
1753	korporativnaya-i-promyshlennaya-odezhda	Корпоративная и промышленная одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	17	Corporate and industrial clothing	Корпоративная и промышленная одежда	Korporativ va sanoat kiyimlari
1754	mezhsoedineniya	Межсоединения	elektronnye-komponenty	2025-01-20 04:35:19	\N	5930	Interconnections	Межсоединения	O'zaro bog'lanishlar
1755	tkani-i-tekstilnye-prinadlezhnosti	Ткани и текстильные принадлежности	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	71	Fabrics and textile accessories	Ткани и текстильные принадлежности	Matolar va to'qimachilik aksessuarlari
1756	volokno-i-pryazha	Волокно, пряжа, мех и кожа	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	2025-01-21 12:08:09	49	Fiber, yarn, fur and leather	Волокно, пряжа, мех и кожа	Elyaf, ip, mo'yna va teri
1757	muzhskaya-odezhda	Мужская одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	11	Men's clothing	Мужская одежда	Erkaklar kiyimi
1758	notatsiya-i-otdelka	Нотация и отделка	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	2	Notation and decoration	Обозначения и украшения	Belgilash va bezash
1759	spetsialnaya-odezhda-i-dozhdeviki	Специальная одежда и дождевики	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	10	Special clothing and raincoats	Специальная одежда и дождевики	Maxsus kiyim va yomg'ir paltolari
1760	sportivnaya-odezhda	Спортивная одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	3	Sportswear	Спортивная одежда	Sport kiyimlari
1761	netkannye-materialy	Нетканные материалы	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	3	Non-woven materials	Нетканные материалы	To'qilmagan materiallar
1762	kupalniki-i-plyazhnaya-odezhda	Купальники и пляжная одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	0	Swimwear and beachwear	Купальники и пляжная одежда	Suzish va plyaj kiyimlari
1763	nizhnee-bele-i-odezhda-dlya-sna	Нижнее белье и одежда для сна	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	3	Underwear and sleepwear	Нижнее белье и одежда для сна	Ichki kiyim va uyqu kiyimi
1764	zhenskaya-odezhda	Женская одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	14	Women's clothing	Женская одежда	Ayollar kiyimi
1765	stroitelnoe-mekhanicheskoe-i-elektricheskoe-oborudovanie	Строительное механическое и электрическое оборудование	promyshlennye-postavki	2025-01-20 04:35:19	\N	276	Construction mechanical and electrical equipment	Строительное механическое и электрическое оборудование	Qurilish mexanik va elektr jihozlari
1766	svadebnaya-odezhda	Свадебная одежда	modnaya-odezhda-i-tkani	2025-01-20 04:35:19	\N	0	Wedding clothes	Свадебная одежда	To'y liboslari
1767	promyshlennye-komponenty	Промышленные компоненты	promyshlennye-postavki	2025-01-20 04:35:19	\N	3425	Industrial components	Промышленные компоненты	Sanoat komponentlari
1768	promyshlennye-izmereniya,-ispytaniya-i-inspektsii	Промышленные измерения, испытания и инспекции	promyshlennye-postavki	2025-01-20 04:35:19	\N	254	Industrial Measurements, Testing and Inspection	Промышленные измерения, испытания и инспекции	Sanoat o'lchovlari, sinovlari va nazorati
1769	promyshlennye-tovary	Промышленные товары	promyshlennye-postavki	2025-01-20 04:35:19	\N	824	Industrial goods	Промышленные товары	Sanoat tovarlari
1770	promyshlennaya-bezopasnost-i-obespechenie-pravoporyadka	Промышленная безопасность и обеспечение правопорядка	promyshlennye-postavki	2025-01-20 04:35:19	\N	91	Industrial safety and law enforcement	Промышленная безопасность и обеспечение правопорядка	Sanoat xavfsizligi va huquqni muhofaza qilish
1771	stroitelnye-materialy	Строительные материалы	promyshlennye-postavki	2025-01-20 04:35:19	\N	1070	Building materials	Строительные материалы	Qurilish materiallari
1780	audiooborudovanie-i-naushniki	Аудиооборудование и наушники	bitovaya-elektronika	2025-01-20 06:25:26	\N	56	Audio equipment and headphones	Аудиооборудование и наушники	Audio uskunalar va naushniklar
1781	audio/video-aksessuary	Аудио/Видео аксессуары	bitovaya-elektronika	2025-01-20 06:25:26	\N	44	Audio/Video Accessories	Аудио/Видео аксессуары	Audio/video aksessuarlar
1782	kamery-i-aksessuary	Камеры и аксессуары	bitovaya-elektronika	2025-01-20 06:25:26	\N	7	Cameras and accessories	Камеры и аксессуары	Kameralar va aksessuarlar
1783	aksessuary-dlya-kompyuterov-i-noutbukov	Аксессуары для компьютеров и ноутбуков	bitovaya-elektronika	2025-01-20 06:25:26	\N	44	Accessories for computers and laptops	Аксессуары для компьютеров и ноутбуков	Kompyuterlar va noutbuklar uchun aksessuarlar
1784	kompyutery-i-noutbuki	Компьютеры и ноутбуки	bitovaya-elektronika	2025-01-20 06:25:26	\N	7	Computers and laptops	Компьютеры и ноутбуки	Kompyuterlar va noutbuklar
1786	televizory-i-video	Телевизоры и видео	bitovaya-elektronika	2025-01-20 06:25:26	\N	98	TVs and Videos	Телевизоры и видео	Televizorlar va videolar
1787	igry	Игры	bitovaya-elektronika	2025-01-20 06:25:26	\N	0	Games	Игры	O'yinlar
1788	setevye-produkty	Сетевые продукты	bitovaya-elektronika	2025-01-20 06:25:26	\N	96	Network products	Сетевые продукты	Tarmoq mahsulotlari
1789	kompyuternye-podsistemy	Компьютерные подсистемы	bitovaya-elektronika	2025-01-20 06:25:26	\N	2	Computer subsystems	Компьютерные подсистемы	Kompyuterning quyi tizimlari
1790	naruzhnaya-elektronika	Наружная электроника	bitovaya-elektronika	2025-01-20 06:25:26	\N	760	Outdoor electronics	Наружная электроника	Tashqi elektronika
1791	drony,-aksessuary-i-zapchasti	Дроны, аксессуары и запчасти	bitovaya-elektronika	2025-01-20 06:25:26	\N	0	Drones, accessories and spare parts	Дроны, аксессуары и запчасти	Dronlar, aksessuarlar va ehtiyot qismlar
1792	detali-bytovoy-tekhniki	Детали бытовой техники	bitovaya-tekhnika	2025-01-21 09:27:09	\N	575	Household appliance parts	Детали бытовой техники	Maishiy texnika qismlari
1793	bytovaya-tekhnika	Бытовая техника	bitovaya-tekhnika	2025-01-21 09:27:09	\N	1105	Household appliances	Бытовая техника	Maishiy texnika
1795	d7d518bb-82ed-4c76-9913-e19f6288965e	Детская одежда	modnaya-odezhda-i-tkani	2025-01-21 10:04:04.511289	\N	8	Children's clothing	Детская одежда	Bolalar kiyimlari
1796	21678170-5988-4840-9b8b-5e29c2773a70	Бобовые продукты	eda-i-napitki	2025-01-21 10:37:42.735186	\N	10	Legumes	Бобовые продукты	Dukkaklilar
1797	ef565acf-0057-47d8-af8b-b6fdd59d7036	Выпечка	eda-i-napitki	2025-01-21 10:37:46.815923	\N	0	Baking	Выпечка	Pishirish
1798	d3f6cde9-73f0-46d6-8f48-256a85018848	Детское питание	eda-i-napitki	2025-01-21 10:37:52.26411	\N	0	Baby food	Детское питание	Bolalar ovqati
1799	ec8613ce-7249-4709-bb03-b284bc4a1837	Еда быстрого приготовления	eda-i-napitki	2025-01-21 10:37:58.470219	\N	4	Fast food	Еда быстрого приготовления	Fastfud
1800	5f63c9eb-1684-4ac0-b8c1-13d95b7cd4cb	Закуски	eda-i-napitki	2025-01-21 10:38:02.751356	\N	0	Snacks	Закуски	Aperatiflar
1801	36cd8a04-424b-426b-9a1a-d9687bb81eb0	Зерновые продукты	eda-i-napitki	2025-01-21 10:38:08.013581	\N	1	Cereal products	Зерновые продукты	Don mahsulotlari
1802	251cdcf2-5d09-46ea-98be-be2259ffcbf8	Кондитерские изделия	eda-i-napitki	2025-01-21 10:38:15.283873	\N	0	Confectionery	Кондитерские изделия	Qandolat mahsulotlari
1803	6994254d-780f-47d8-98d4-dc39d38a8242	Консервированные продукты	eda-i-napitki	2025-01-21 10:38:21.620088	\N	0	Canned goods	Консервированные продукты	Konservalangan mahsulotlar
1804	4ed028db-fd2b-495b-a764-fbc4f8364d0b	Кофе	eda-i-napitki	2025-01-21 10:38:25.977966	\N	0	Coffee	Кофе	Kofe
1805	05112725-b737-4f8a-873a-48c5998b389b	Мёд и медовые продукты	eda-i-napitki	2025-01-21 10:38:34.294756	\N	0	Honey and honey products	Мёд и медовые продукты	Asal va asal mahsulotlari
1806	aa536b4d-e7ce-47e0-b6a8-af4658ded7a1	Молочные продукты	eda-i-napitki	2025-01-21 10:38:40.302205	\N	0	Dairy products	Молочные продукты	Sut mahsulotlari
1807	9faa0e5d-2711-4027-a25a-1e5920d579ee	Морепродукты	eda-i-napitki	2025-01-21 10:38:44.817262	\N	0	Seafood	Морепродукты	Dengiz mahsulotlari
1808	018996d1-4c0b-4674-9cc9-a1fd44b730e6	Мясо	eda-i-napitki	2025-01-21 10:38:49.348517	\N	1	Meat	Мясо	Go'sht
1809	64e0e859-3c02-43df-b3b9-462fb4b0c727	Овощные продукты	eda-i-napitki	2025-01-21 10:38:55.902571	\N	0	Vegetable products	Овощные продукты	Sabzavot mahsulotlari
1810	75923755-2a77-4775-a550-39181020b892	Питьевая вода	eda-i-napitki	2025-01-21 10:39:01.176826	\N	1	Drinking water	Питьевая вода	Ichimlik suvi
1811	81d05d1b-8849-479b-926e-3730b413b41d	Пищевые ингредиенты	eda-i-napitki	2025-01-21 10:39:08.971087	\N	0	Food Ingredients	Пищевые ингредиенты	Oziq-ovqat ingredientlari
1812	b2eaf164-6191-443e-833b-920eabf41813	Приправы и специи	eda-i-napitki	2025-01-21 10:39:14.703959	\N	0	Seasonings and spices	Приправы и специи	Ziravorlar va ziravorlar
1813	893b3e4f-bb07-456b-a271-ee456aa5b996	Фруктовые продукты	eda-i-napitki	2025-01-21 10:39:20.48587	\N	0	Fruit products	Фруктовые продукты	Meva mahsulotlari
1814	861f6e69-08e9-42a4-a0ea-b3c63dc312ea	Чай	eda-i-napitki	2025-01-21 10:39:25.154879	\N	0	Tea	Чай	Choy
1815	7e6ec5b4-8a72-4f24-b3b0-fd5a756bf815	Яичные продукты	eda-i-napitki	2025-01-21 10:39:31.261416	\N	0	Egg products	Яичные продукты	Tuxum mahsulotlari
1816	6361676a-d9a8-4b87-9d84-fd950fabcb65	Другие продукты питания и напитки	eda-i-napitki	2025-01-21 10:39:37.251416	\N	77	Other food and drinks	Другие продукты питания и напитки	Boshqa oziq-ovqat va ichimliklar
1817	6c49b195-1c83-4456-9606-591e4e3cc2c8	Мониторы	bitovaya-elektronika	2025-01-21 11:08:37.560169	\N	0	Monitors	Мониторы	Monitorlar
1818	a66a74a5-3d80-436b-a087-9cc554fc9fcf	Ноутбуки	mobilnaya-elektronika	2025-01-21 11:09:59.158205	\N	1	Laptops	Ноутбуки	Noutbuklar
1819	e4e1ba17-d94c-4528-8db1-b7234466da4a	Лекарственные средства	zdravookhranenie	2025-01-21 11:16:08.843904	\N	0	Medicines	Лекарственные средства	Dorilar
1820	0cf612c2-eb6c-42ff-a5bf-11f61c56db5a	Ветеринария	zdravookhranenie	2025-01-21 11:16:38.348752	\N	0	Veterinary medicine	Ветеринарная медицина	Veterinariya tibbiyoti
1821	3f66495f-6cac-4407-ae0a-c2da3fdabb01	Фитотерапия	zdravookhranenie	2025-01-21 11:16:58.179718	\N	0	Phytotherapy	Фитотерапия	Fitoterapiya
1822	c1ca465b-b454-4dee-bdd4-58b0c7f13abf	Аксессуары для мебели	mebel-i-domashniy-dekor	2025-01-21 11:17:41.117388	\N	0	Furniture accessories	Аксессуары для мебели	Mebel aksessuarlari
1823	77712f49-5a40-4ff5-96bf-ccf1239973d3	Детская мебель	mebel-i-domashniy-dekor	2025-01-21 11:17:53.752714	\N	4	Children's furniture	Детская мебель	Bolalar mebellari
1824	faa6dc8d-41be-4fef-b3bb-c5c43e5a1fef	Мебельная фурнитура	mebel-i-domashniy-dekor	2025-01-21 11:18:17.687853	\N	1	Furniture fittings	Мебельная фурнитура	Mebel jihozlari
1825	a8c948d0-ff2a-4d2e-8b26-ad6186ed5fdb	Детали обуви и аксессуары	modnye-aksessuary-i-obuv	2025-01-21 11:51:16.386185	\N	0	Shoe parts and accessories	Детали обуви и аксессуары	Poyafzal qismlari va aksessuarlari
1826	b5b1ad90-0304-4c1e-876f-ef63f865acfd	Детская обувь	modnye-aksessuary-i-obuv	2025-01-21 11:51:22.989151	\N	0	Children's shoes	Детская обувь	Bolalar poyabzali
1827	8bbbf0d1-7336-4ded-91a1-f8b45c8e52fa	Оборудование для ремонта обуви	modnye-aksessuary-i-obuv	2025-01-21 11:51:31.091798	\N	0	Shoe repair equipment	Оборудование для ремонта обуви	Oyoq kiyimlarini ta'mirlash uchun uskunalar
1828	070ffc92-79a4-4024-946a-9a5cf49d7bdd	Обувные материалы	modnye-aksessuary-i-obuv	2025-01-21 11:51:38.308707	\N	0	Shoe materials	Обувные материалы	Poyafzal materiallari
1829	6db6debc-be00-47c2-959d-b56692896777	Обувь для новорожденных	modnye-aksessuary-i-obuv	2025-01-21 11:51:44.544815	\N	0	Shoes for newborns	Обувь для новорожденных	Yangi tug'ilgan chaqaloqlar uchun poyabzal
1830	2be69827-66b5-491c-90d0-c69477d17342	Обувь специального назначения	modnye-aksessuary-i-obuv	2025-01-21 11:51:54.01722	\N	0	Special purpose footwear	Обувь специального назначения	Maxsus maqsadli poyabzal
1831	51b0db95-580f-4945-8cd0-54e12d2c3356	Аксессуары для волос	modnye-aksessuary-i-obuv	2025-01-21 11:53:55.226084	\N	0	Hair Accessories	Аксессуары для волос	Soch uchun aksessuarlar
1832	97754d9d-26c4-44e8-a911-0dec02aefd09	Галстуки и аксессуары к ним	modnye-aksessuary-i-obuv	2025-01-21 11:54:00.323183	\N	0	Ties and accessories	Галстуки и аксессуары к ним	Bog'lamlar va aksessuarlar
1833	77d50ec1-8cab-49bf-a384-eccb379281a4	Другие аксессуары моды и стиля	modnye-aksessuary-i-obuv	2025-01-21 11:54:06.478727	\N	0	Other fashion and style accessories	Другие аксессуары моды и стиля	Moda va uslubdagi boshqa aksessuarlar
1834	4c9ba52c-0f33-4c3b-a943-695dd5df2c11	Головные уборы	modnye-aksessuary-i-obuv	2025-01-21 11:54:19.961373	\N	0	Headwear	Головные уборы	Bosh kiyim
1835	2e64996b-7a91-47ac-b89b-26650b9e6982	Перчатки и варежки	modnye-aksessuary-i-obuv	2025-01-21 11:54:29.277125	\N	0	Gloves and mittens	Перчатки и варежки	Qo'lqoplar va qo'lqoplar
1836	f9b76966-97c0-4ff2-9348-4addfb57c2a6	Ремни	modnye-aksessuary-i-obuv	2025-01-21 11:54:33.781454	\N	0	Belts	Ремни	Kamarlar
1837	dc74c210-a4c6-4ce7-84b9-6fe3804388b8	Шарфы и платки	modnye-aksessuary-i-obuv	2025-01-21 11:54:40.246577	\N	0	Scarves and shawls	Шарфы и платки	Sharflar va sharflar
1838	938219d3-dda7-438b-97de-06b150b7e948	Бижутерия	modnye-aksessuary-i-obuv	2025-01-21 11:55:04.927	\N	7	Jewelry	Ювелирные изделия	Zargarlik buyumlari
1839	d7e0bfe4-d7b9-4b41-a23e-b5163eb36b94	Внутреннее освещение	mebel-i-domashniy-dekor	2025-01-21 11:58:08.218861	\N	1	Interior lighting	Внутреннее освещение	Ichki yoritish
1840	81d178c7-4e87-41a8-a45a-05777a6d7abd	Наружное освещение	mebel-i-domashniy-dekor	2025-01-21 11:58:13.783889	\N	0	Outdoor lighting	Наружное освещение	Tashqi yoritish
1841	51ea5ff3-d3e1-48af-96ff-05b01522b488	Осветительные лампы	mebel-i-domashniy-dekor	2025-01-21 11:58:18.557143	\N	0	Lighting lamps	Осветительные лампы	Yoritish lampalari
1842	bf67224b-c21a-4442-94b0-93c4f3216497	Праздничное освещение	mebel-i-domashniy-dekor	2025-01-21 11:58:23.523392	\N	0	Festive Lighting	Праздничное освещение	Bayram yorug'ligi
1843	e395f114-8d6c-4138-a41d-c8c22c6d0b22	Профессиональное освещение	mebel-i-domashniy-dekor	2025-01-21 11:58:28.77332	\N	0	Professional lighting	Профессиональное освещение	Professional yoritish
1844	83b6460b-42bd-4c8b-a524-2c8ba6a97f16	Светодиодное освещение	mebel-i-domashniy-dekor	2025-01-21 11:58:34.825212	\N	0	LED lighting	Светодиодное освещение	LED yoritish
1845	fd107aef-6551-4382-a86a-262a51369d4b	Школьные принадлежности	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-21 12:05:24.150122	\N	0	School supplies	Школьные принадлежности	Maktab anjomlari
1846	195ca9aa-7f79-4f6a-a425-22de80f6f167	Прочее для офиса	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-21 12:05:42.704272	\N	0	Other for the office	Прочее для офиса	Ofis uchun boshqa
1847	7a71067f-afbf-43f6-8e11-11b8e4270f3f	Прочее для школы	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-21 12:05:48.815506	\N	0	Other for school	Прочее для школы	Maktab uchun boshqa
1848	ed0093ce-1468-48ef-baf8-29642c5ea105	Прочее для творчества	shkolnye-i-ofisnye-prinadlezhnosti	2025-01-21 12:06:02.148325	2025-01-21 12:06:15	0	Other stuff for creativity	Прочее для творчества	Ijodkorlik uchun boshqa narsalar
1849	6f14d899-9b05-4a74-a8aa-9fdc53e1afc1	Переработка отходов	promyshlennye-postavki	2025-01-21 12:10:21.341954	\N	0	Waste recycling	Переработка отходов	Chiqindilarni qayta ishlash
1850	57fa5fd9-9f7a-47a4-bee5-56e79531ecff	Санитарная канализация	promyshlennye-postavki	2025-01-21 12:10:26.521829	\N	1	Sanitary sewerage	Санитарная канализация	Sanitariya kanalizatsiyasi
1851	82d6c232-8e47-429d-8ff9-7cb88daffef3	Другие экологические продукты	promyshlennye-postavki	2025-01-21 12:10:32.189057	\N	0	Other eco-friendly products	Другие экологические продукты	Boshqa ekologik toza mahsulotlar
1852	175569fe-2ff4-4800-a3e5-96a78ca8eb20	Другие избыточные запасы	promyshlennye-postavki	2025-01-21 12:10:36.657176	\N	0	Other excess stocks	Другие избыточные запасы	Boshqa ortiqcha zaxiralar
1853	8cc1e3cf-5978-4b92-81b6-eb4a6674fa58	Народные художественные изделия	podarki,-festivali-i-khobbi	2025-01-21 12:15:51.571521	\N	0	Folk art products	Народные художественные изделия	Xalq ijodiyoti mahsulotlari
1854	bba0fb6c-6526-4f27-85cc-b793c33a50c5	Ремесленные изделия	podarki,-festivali-i-khobbi	2025-01-21 12:16:09.312299	\N	0	Handicrafts	Ремесленные изделия	Qo'l san'atlari
1855	700ceb53-981a-485b-b3a8-ed46c78162bc	Свечи	podarki,-festivali-i-khobbi	2025-01-21 12:16:15.744665	\N	0	Candles	Свечи	Shamlar
1856	f8618eb9-c967-4c73-aa7d-92b34ac5f0b9	Кошельки и портмоне	modnye-aksessuary-i-obuv	2025-01-21 12:17:23.606696	\N	1	Wallets and purses	Кошельки и портмоне	Hamyonlar va hamyonlar
1860	50a5cb71-f6ac-45b1-9ca1-4d6003fe525b	Переработанная резина	khimikati	2025-01-21 12:20:16.404212	\N	0	Recycled rubber	Переработанная резина	Qayta ishlangan kauchuk
1861	2c2c7bde-33f9-4ef2-a68e-d6093867b2dd	Переработанный пластик	khimikati	2025-01-21 12:20:20.754177	\N	0	Recycled plastic	Переработанный пластик	Qayta ishlangan plastmassa
1862	b9d18263-7cd7-4d10-9a2e-f57e237ca75c	Пластиковые изделия	khimikati	2025-01-21 12:20:25.921248	\N	0	Plastic products	Пластиковые изделия	Plastik mahsulotlar
1863	2e72ebea-7c99-4a92-98e7-a83c426dbabb	Резиновое сырье	khimikati	2025-01-21 12:20:32.368479	\N	0	Rubber raw materials	Резиновое сырье	Kauchuk xom ashyo
1864	6c3ca82d-58d5-4701-a00d-81e678f7fff6	Резиновые изделия	khimikati	2025-01-21 12:20:38.670098	\N	0	Rubber products	Резиновые изделия	Kauchuk mahsulotlar
1865	00ed865b-60b7-43f8-8c9d-c8456265a012	Дисплеи	elektronnye-komponenty	2025-01-21 12:21:42.637665	\N	0	Displays	Дисплеи	Displeylar
1866	3f1abe84-a8b0-44f9-b601-65e199d73a6e	Электронные системы данных	elektronnye-komponenty	2025-01-21 12:22:00.513278	\N	0	Electronic data systems	Электронные системы данных	Elektron ma'lumotlar tizimlari
1867	2bd9f355-086c-44ad-ac3e-3cce88b21d1e	Материалы	elektronnye-komponenty	2025-01-21 12:22:36.376122	\N	1	Materials	Материалы	Materiallar
1869	33fbd123-6fee-4f0a-b1b6-f0cdc55b9b20	Очки	modnye-aksessuary-i-obuv	2025-01-21 12:24:13.436723	\N	0	Glasses	Очки	Ko'zoynak
1870	0a6aab7c-80b4-4763-ac4b-470d6d167d4e	Парк развлечений	sport-i-otdykh-na-prirode	2025-01-21 12:40:06.483929	\N	26	Amusement park	Парк развлечений	Istirohat bog'i
1871	fa762d85-19be-4626-9b3d-f0b1c05a0f1a	Фитнес и бодибилдинг	sport-i-otdykh-na-prirode	2025-01-21 12:56:12.23492	\N	0	Fitness and bodybuilding	Фитнес и бодибилдинг	Fitnes va bodibilding
1872	8a530f74-8f00-4a86-9118-eb0052d53589	Музыкальные инструменты	sport-i-otdykh-na-prirode	2025-01-21 12:56:28.25559	\N	0	Musical instruments	Музыкальные инструменты	Musiqiy asboblar
1873	1b470e54-549a-4289-b230-e9193a8911b4	Защитные экипировки	sport-i-otdykh-na-prirode	2025-01-21 12:56:43.222768	\N	0	Protective equipment	Защитные экипировки	Himoya uskunalari
1874	65c35d12-1eb5-4e40-b42c-203997b76fbc	Другие товары для спорта и развлечений	sport-i-otdykh-na-prirode	2025-01-21 12:56:51.157792	\N	0	Other sports and entertainment products	Другие товары для спорта и развлечений	Boshqa sport va ko'ngilochar mahsulotlar
1875	42a11477-4b22-423b-81d5-edcf6d1b79a3	Аксессуары для общей техники	mashiny-i-oborudovanie	2025-01-21 12:59:44.115742	\N	2	Accessories for general appliances	Аксессуары для общей техники	Umumiy jihozlar uchun aksessuarlar
1876	6d773195-cd8d-4612-a7ec-57695eb79259	Деревообрабатывающее оборудование	mashiny-i-oborudovanie	2025-01-21 12:59:50.654469	\N	0	Woodworking equipment	Деревообрабатывающее оборудование	Yog'ochga ishlov berish uskunalari
1877	6a9fae86-351f-4e49-bb55-cc1eea238b39	Инженерные и строительные машины	mashiny-i-oborudovanie	2025-01-21 12:59:55.442769	\N	0	Engineering and construction machines	Инженерные и строительные машины	Muhandislik va qurilish mashinalari
1878	fdfe557e-bf32-448c-9248-1170e86c68a9	Машиностроение	mashiny-i-oborudovanie	2025-01-21 13:00:00.657176	\N	1	Mechanical engineering	Машиностроение	Mashinasozlik
1879	8e13f572-3fb1-4deb-a8f9-ac5a6ece4ca4	Металлообрабатывающие оборудования	mashiny-i-oborudovanie	2025-01-21 13:00:06.027907	\N	0	Metalworking equipment	Металлообрабатывающие оборудования	Metallga ishlov berish uskunalari
1880	61ec128f-9f8a-475d-b6d3-4355ebcb5925	Оборудование для пластмассы и резины	mashiny-i-oborudovanie	2025-01-21 13:00:11.077845	\N	0	Equipment for plastics and rubber	Оборудование для пластмассы и резины	Plastmassa va kauchuk uchun uskunalar
1881	52126990-7b84-4dd1-bb62-578543f45f54	Оборудование для производства бумаги	mashiny-i-oborudovanie	2025-01-21 13:00:15.340391	\N	1	Paper production equipment	Оборудование для производства бумаги	Qog'oz ishlab chiqarish uskunalari
1882	d3a98037-5a12-4878-878d-f232ed63db99	Оборудование для производства домашней продукции	mashiny-i-oborudovanie	2025-01-21 13:00:19.772508	\N	0	Equipment for the production of home products	Оборудование для производства домашней продукции	Uy mahsulotlarini ishlab chiqarish uchun uskunalar
1883	450d57c1-ebb8-4a28-adcb-fdb72c4d4410	Оборудование для производства продуктов питания и напитков	mashiny-i-oborudovanie	2025-01-21 13:00:26.293222	\N	16	Equipment for food and beverage production	Оборудование для производства продуктов питания и напитков	Oziq-ovqat va ichimliklar ishlab chiqarish uchun uskunalar
1884	72c1cd04-f2c2-4110-8197-417f5b74a352	Оборудования для производства электроники	mashiny-i-oborudovanie	2025-01-21 13:00:34.395619	\N	0	Equipment for electronics manufacturing	Оборудования для производства электроники	Elektron ishlab chiqarish uchun uskunalar
1885	6d98aed2-7f1b-4f17-beea-e52c865634b6	Оборудования для строительных материалов	mashiny-i-oborudovanie	2025-01-21 13:00:39.191141	\N	0	Equipment for building materials	Оборудования для строительных материалов	Qurilish materiallari uchun uskunalar
1886	1a089a73-8236-4927-9a4f-223038855c3f	Оборудования для химического и фармацевтического производства	mashiny-i-oborudovanie	2025-01-21 13:00:43.890768	\N	0	Equipment for chemical and pharmaceutical production	Оборудования для химического и фармацевтического производства	Kimyoviy va farmatsevtika ishlab chiqarish uchun uskunalar
1887	e82c004a-43a0-4ed1-b81b-5921e51eee5c	Общепромышленное оборудование	mashiny-i-oborudovanie	2025-01-21 13:00:49.292253	\N	12	General industrial equipment	Общепромышленное оборудование	Umumiy sanoat uskunalari
1888	cb31db3b-642f-4055-9e55-36f68727d228	Переработка минерального сырья и энергетическое оборудование	mashiny-i-oborudovanie	2025-01-21 13:00:54.598425	\N	2	Mineral processing and energy equipment	Переработка минерального сырья и энергетическое оборудование	Minerallarni qayta ishlash va energiya uskunalari
1889	80129d34-bad4-458c-baf1-b9a18a1d2246	Печатные машины	mashiny-i-oborudovanie	2025-01-21 13:00:58.759904	\N	0	Printing machines	Печатные машины	Bosib chiqarish mashinalari
1890	4ad5661c-0943-471a-bcb8-99f50292bac7	Погрузочно-разгрузочное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:03.024486	\N	0	Loading and unloading equipment	Погрузочно-разгрузочное оборудование	Yuklash va tushirish uskunalari
1891	3f69c478-c113-4d72-ba1a-2f09c027e2c3	Промышленное лазерное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:08.111577	\N	0	Industrial laser equipment	Промышленное лазерное оборудование	Sanoat lazer uskunalari
1892	83539e1b-33a9-4930-bb93-6e565f84d3a6	Промышленные роботы	mashiny-i-oborudovanie	2025-01-21 13:01:12.433496	\N	0	Industrial robots	Промышленные роботы	Sanoat robotlari
1893	4683f4e7-cf20-4270-b07d-68fd4ff50ad8	Прочее промышленное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:17.450272	\N	1	Other industrial equipment	Прочее промышленное оборудование	Boshqa sanoat uskunalari
1894	075548be-6269-4937-8162-0c85e2fb0e8a	Сварочное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:21.657106	\N	0	Welding equipment	Сварочное оборудование	Payvandlash uskunalari
1895	0b5344ce-55f0-41c7-b94d-5c95595cf891	Сельхоз машины и оборудования	mashiny-i-oborudovanie	2025-01-21 13:01:27.040705	\N	1	Agricultural machinery and equipment	Сельхоз машины и оборудования	Qishloq xo'jaligi mashinalari va uskunalari
1896	adb41edd-91b9-490e-b64d-c4df5394c20c	Станочное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:33.553056	\N	0	Machine tools	Станочное оборудование	Mashina asboblari
1897	a8084a9f-7a6e-4b68-8ad7-561a79b1e524	Текстильные и швейные оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:38.472671	\N	0	Textile and sewing equipment	Текстильные и швейные оборудование	To'qimachilik va tikuv uskunalari
1898	9166a200-0735-430e-a169-b7af26cc581e	Упаковочные машины	mashiny-i-oborudovanie	2025-01-21 13:01:44.956351	\N	1	Packaging machines	Упаковочные машины	Qadoqlash mashinalari
1899	db0ee1d6-db43-422a-ac8a-abf4fe70cac5	Холодильное и теплообменное оборудование	mashiny-i-oborudovanie	2025-01-21 13:01:49.631965	\N	11	Refrigeration and heat exchange equipment	Холодильное и теплообменное оборудование	Sovutgich va issiqlik almashinuvi uskunalari
1900	bc5d181d-cba2-4027-af9c-5ebd1a890040	Экологическая техника	mashiny-i-oborudovanie	2025-01-21 13:01:54.277764	\N	1	Ecological technology	Экологическая техника	Ekologik texnologiya
1901	22984023-9f6c-4f0d-9f54-25fba56d6fbb	Детские игрушки	sport-i-otdykh-na-prirode	2025-01-21 13:13:57.749554	\N	3	Children's toys	Детские игрушки	Bolalar o'yinchoqlari
1902	5e3f32c0-d677-4cfe-8329-a365f1b00dec	Одежда для беременных	modnaya-odezhda-i-tkani	2025-01-21 13:14:19.415912	\N	0	Maternity clothes	Одежда для беременных	Onalik kiyimlari
1903	4edc261c-6a1f-4f50-a40f-d4387cfccef8	Аксессуары для детей	modnye-aksessuary-i-obuv	2025-01-21 13:15:22.198555	\N	4	Accessories for children	Аксессуары для детей	Bolalar uchun aksessuarlar
1904	66a10869-9da5-4ac2-8c63-ad602d748ef8	Для беременных, мам и младенцев	modnaya-odezhda-i-tkani	2025-01-21 13:17:15.853457	\N	2	For pregnant women, mothers and babies	Для беременных, мам и младенцев	Homilador ayollar, onalar va chaqaloqlar uchun
1905	d06b0d6e-b561-4a4a-b136-ea71c660f5da	Литература	zdravookhranenie	2025-01-21 13:19:42.251494	\N	1	Literature	Литература	Adabiyot
1906	88a47bc1-2bad-4f5d-9c3f-6763340c086a	Товары для детей	zdravookhranenie	2025-01-21 13:21:37.134867	\N	1	Products for children	Товары для детей	Bolalar uchun mahsulotlar
1908	f4137a80-3bbd-4d40-9e18-a50b4332c4a3	Домашний декор	domashnie-i-domashnie-zhivotnye	2025-01-21 13:23:51.471191	\N	1	Home decor	Домашний декор	Uy dekoratsiyasi
1909	8ba2de34-44a0-4e42-a5ab-9b9aa8d90602	Прочее	domashnie-i-domashnie-zhivotnye	2025-01-21 13:23:58.461088	\N	9	Other	Прочее	Boshqa
1910	e55db434-f79a-4c23-ab0a-4ffcc3e079cc	Продукты для домашних животных	eda-i-napitki	2025-01-21 13:24:51.47813	\N	0	Products for pets	Продукты для домашних животных	Uy hayvonlari uchun mahsulotlar
1911	f0dcbb09-8977-49f7-ac05-5b4e4874a439	Садовые принадлежности	domashnie-i-domashnie-zhivotnye	2025-01-21 13:25:09.904152	\N	0	Garden accessories	Садовые принадлежности	Bog'dagi aksessuarlar
1912	0cfe235a-d260-4054-b2ff-238f2e6739da	Товары для ванной	domashnie-i-domashnie-zhivotnye	2025-01-21 13:25:27.055715	\N	60	Bathroom products	Товары для ванной	Hammom mahsulotlari
1913	c47c3ae4-02f3-42d3-ab00-40dcf04bedef	Биоразлагаемая упаковка	pechat-i-upakovka	2025-01-21 14:12:26.376078	\N	0	Biodegradable packaging	Биоразлагаемая упаковка	Biologik parchalanadigan qadoqlash
1914	2301c69c-7ba7-4ee2-8821-0b99a0b746c3	Бочки и ведра	pechat-i-upakovka	2025-01-21 14:12:31.113744	\N	0	Barrels and buckets	Бочки и ведра	Bochkalar va chelaklar
1915	1097363e-4286-4e78-bffe-89f8ab2ca133	Бумага и картон	pechat-i-upakovka	2025-01-21 14:12:35.293455	\N	0	Paper and cardboard	Бумага и картон	Qog'oz va karton
1916	611e4404-ed85-4cd6-80f3-4d5e58aa9b9d	Деревянная упаковка	pechat-i-upakovka	2025-01-21 14:12:39.19145	\N	0	Wooden packaging	Деревянная упаковка	Yog'och qadoqlash
1917	03a6678d-d6e2-48f0-912d-7f613cc766a9	Металлическая упаковка	pechat-i-upakovka	2025-01-21 14:12:43.868833	\N	0	Metal packaging	Металлическая упаковка	Metall qadoqlash
1918	37f8aa96-734c-4ff0-a33d-3b63b22123d1	Пластиковая упаковка	pechat-i-upakovka	2025-01-21 14:12:48.206131	\N	0	Plastic packaging	Пластиковая упаковка	Plastik qadoqlash
1919	aab6152a-36ab-4536-bb37-c798bf9a5e94	Поддоны	pechat-i-upakovka	2025-01-21 14:12:56.194143	\N	0	Pallets	Поддоны	Paletalar
1920	8755ad82-3373-415e-9f44-03438fe16bcd	Пульпа, древесная масса	pechat-i-upakovka	2025-01-21 14:13:03.591542	\N	0	Pulp, wood pulp	Пульпа, древесная масса	Pulpa, yog'och xamiri
1921	ce581848-0cd3-42c6-8b4e-eb416c67b8d2	Рукоятки	pechat-i-upakovka	2025-01-21 14:13:07.664964	\N	0	Handles	Рукоятки	Tutqichlar
1922	05883b1c-2417-433b-b1cd-edbe37fa29ee	Скотч, пленка, бумага	pechat-i-upakovka	2025-01-21 14:13:12.88838	\N	1	Scotch tape, film, paper	Скотч, пленка, бумага	Skotch, plyonka, qog'oz
1923	822d9b32-1723-4199-a59a-8b3d0ce668a2	Стеклянная упаковка	pechat-i-upakovka	2025-01-21 14:13:16.458235	\N	2	Glass packaging	Стеклянная упаковка	Shisha qadoqlash
1924	9029e693-b001-45c3-8d0b-5ff0378afa64	Текстильная упаковка	pechat-i-upakovka	2025-01-21 14:13:20.415695	\N	0	Textile packaging	Текстильная упаковка	To'qimachilik qadoqlash
1925	84aa31ec-f831-4140-a548-512c998923ac	Транспортировочная упаковка	pechat-i-upakovka	2025-01-21 14:13:24.691636	\N	0	Transport packaging	Транспортировочная упаковка	Transport qadoqlash
1926	e6661dac-5ea4-4a7c-91bd-2e9acab81c8c	Упаковка из органзы	pechat-i-upakovka	2025-01-21 14:13:28.859486	\N	0	Organza packaging	Упаковка из органзы	Organza qadoqlash
1927	7221936e-519b-4694-b80b-1dc9201bf473	Упаковка из прочих материалов	pechat-i-upakovka	2025-01-21 14:13:32.875785	\N	2	Packaging made from other materials	Упаковка из прочих материалов	Boshqa materiallardan qadoqlash
1928	36fe814f-04e0-44a9-a5dc-47c41af11cb6	Упаковочная веревка	pechat-i-upakovka	2025-01-21 14:13:38.987325	\N	0	Packing rope	Упаковочная веревка	Qadoqlash arqon
1929	657221e5-4562-44a4-96d0-437c12939d58	Вилочные погрузчики	344b927d-ca9d-4e98-9338-19ad5f16d828	2025-04-03 13:12:48.966249	\N	12	Forklifts	Вилочные погрузчики	Forkliftlar
1930	d1976cda-2565-4aa5-9cd6-d37a4e4ee678	Другое	75eff93b-9170-4360-97fd-5ec3f5964d76	2025-04-14 09:29:53.909384	\N	1	Other	Другое	Boshqa
1931	c5c5d735-ecc4-4e91-82c2-8a23d78cb54f	Масла и смазочные материалы	mashiny-i-oborudovanie	2025-06-04 12:20:36.856437	\N	131	Oils and lubricants	Масла и смазочные материалы	Yog'lar va yog'lash materiallari
\.


--
-- Data for Name: user_categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_categories (id, user_id, category_id, name, created_at, updated_at) FROM stdin;
1	52	user_cat_bc555a8c-7e8b-4486-aa3d-c2fefedbc0b4	Фрукты	2025-07-29 09:53:44	2025-07-29 09:53:44
4	52	user_cat_d40bf7da-3a15-4481-b07d-be2852704c85	Овощи	2025-07-29 10:04:25	2025-07-29 10:04:25
11	47	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	Строительные материалы	2025-07-29 11:23:40	2025-07-29 11:23:40
\.


--
-- Data for Name: user_subcategories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_subcategories (id, user_id, subcategory_id, name, category_id, created_at, updated_at) FROM stdin;
7	47	user_subcat_769e0d57-32e9-422e-8564-79f49e11629d	Краска	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:23:56	2025-07-29 11:23:56
8	47	user_subcat_888fd087-791a-4452-94f7-1c1501fe3acc	Бетон	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:24:02	2025-07-29 11:24:02
9	47	user_subcat_4348f501-a8b3-481a-a91c-7a0ec0426c0e	Клей и смазочные материалы	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:24:14	2025-07-29 11:24:14
10	47	user_subcat_ee6cce73-d034-42c2-ae7b-55b2fa3e9302	Облицовочные материалы	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:32:17	2025-07-29 11:32:17
11	47	user_subcat_2bb828db-9a01-42fc-b2d9-e0605ca9d273	Электрика	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:32:29	2025-07-29 11:32:29
12	47	user_subcat_871d3d78-40b9-4eee-8bd4-d7d0551cc543	Сантехника	user_cat_2d9fd26e-234a-4e43-80fb-51265952a765	2025-07-29 11:32:38	2025-07-29 11:32:38
13	52	user_subcat_76e4704a-a3d3-422d-a276-ca1b42ac70af	Цитрусовые	user_cat_bc555a8c-7e8b-4486-aa3d-c2fefedbc0b4	2025-07-29 11:37:27	2025-07-29 11:37:27
1	52	user_subcat_e114cbc0-bb83-445c-a88c-ea503db1a8f8	Яблоки, груши	user_cat_bc555a8c-7e8b-4486-aa3d-c2fefedbc0b4	2025-07-29 09:58:41	2025-07-29 11:38:04
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, user_id, glink, role, acc_type, status_subscription, first_name, last_name, user_name, "position", email, phone_number, verified_email, phone_ok, password, fcm_token, fcm_token_android, timezone, last_logged_in, is_online, language, messages_language, country, city, avatar_url, banned, currency, balance, ref_balance, demo_balance, bonus_balance, inn, comp_pinfl, comp_state, company_type, company_name, company_description, company_rating, com_address, com_leader, comp_logo_url, comp_phone, comp_mail, comp_website_url, company_link, company_statuses, comp_verified, comp_tariff, deal_seen, notification_email, notification_email_deal, notification_email_system, notification_email_chat, notification_email_subscription, notification_sms_chat, notification_sms_custom, notification_sms_system, is_active, catch, reg_date, moderated, gen_key, referer, invite_link, deleted, created_at, updated_at, product_fields_visibility, cats_type) FROM stdin;
50	708f684e-3fbf-42ae-bd9f-44c9ccdacea2	qt5sNCP8NX	0	individual	0	Rudolf7		Rudolf7	Пользователь	rudolfmukhammadiev1@gmail.com		f	f	$2y$12$6ao3dTx34p31B7Vwxlok5e321HwE0AQBtcY04zBguR1u5MPgMw8ju			UTC	2025-07-27T18:46:32.314757Z	t	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1751972323	f	igS5eaZD0sg602rFQJP9jLRI5diSPLY2			f	2025-07-08 10:58:43	2025-07-27 18:46:32	\N	system
51	a24ba32a-5687-43f6-9ea6-77d82bee283e	qIlstyAEea	0	individual	0	Sumbula74		Sumbula74	Пользователь	a_mukhamedov@mail.ru		f	f	$2y$12$BpyQ.IgPdmRxFa1S8zzTgOHSYVpZbJ1WrviQI04zMC.fSZpTg1fDi			UTC	2025-07-15T08:45:54.407184Z	f	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1752569154	f	bVDY2QQ7dBNK4UGjWjln0AFQlwZDmwXT			f	2025-07-15 08:45:54	2025-07-15 08:45:54	\N	system
53	e770f290-6517-470f-b641-d67739d8d0c0	s709D7K6IF	1	individual	0	b2bsklad_admin		b2bsklad_admin	Пользователь	admin@b2bsklad.uz		f	f	$2y$12$8QM8Aul0g7ATzVYEMEH5KeJ9vJI7VU1CNuWrPNnN3TID/u2qzzBNG			UTC	2025-07-22T10:30:10.301511Z	t	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1752733059	f	Txqd33fxMGEhN1gwaSnwxeMf3SDPwR3t			f	2025-07-17 06:17:39	2025-07-22 10:30:10	\N	system
54	9d078baa-0ded-40a6-ab19-2796167760c8	XRSbtSWXW4	0	individual	0	Иван Иванов		Иван Иванов	Пользователь	user@example.com		f	f	$2y$12$K5GWfu7exvpTSmW5voPT3OHfdEfwWAyZvSmJR7Qeeu9c.Vg8AwUSq			UTC	2025-07-18T11:23:43.011438Z	f	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1752837823	f	hguh7Tb3GuBWOECoboZOO2PH9RdjLISF			f	2025-07-18 11:23:43	2025-07-18 11:23:43	\N	system
49	df620c5a-7a11-411c-a53c-dcb589d000d0	J96wo2Kw1x	0	individual	0	edmccain01@gmail.com		edmccain01@gmail.com	Пользователь	edmccain01@gmail.com		f	f	$2y$12$oTO5DuIqEvu2f6G88KJqouihyXq4iYAZ0QwLGh.lvTZRhgpAsw85u			UTC	2025-07-07T15:31:35.775226Z	f	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1751902295	f	y4klilcmtVxK6knNJzzaXkysjyoP1LG4			f	2025-07-07 15:31:35	2025-07-07 15:31:35	\N	system
48	bc59dbfb-d44f-461d-94fe-f207363ff1c0	RfGcQv9cku	0	individual	0	auu05		auu05	Пользователь	mukhammadali.ubaydullayev@gmail.com		f	f	$2y$12$/QBm8su72D79lm37NTlKSOkINEUSDgfv60GFzSsip9GC0zGhTJ3yy			UTC	2025-07-07T15:27:58.958032Z	f	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1751902078	f	QAyv3YucHBGMtQmjnQVnz6B10Y94DszK			f	2025-07-07 15:27:58	2025-07-07 15:27:58	\N	system
56	9e9e6c63-d80a-41af-b4cf-5d318cf0233b	AY4Ace85HB	0	individual	0	edmccain0333@gmail.com		edmccain0333@gmail.com	Пользователь	edmccain0333@gmail.com	+998 23 234 23 52	f	f	$2y$12$7UBWAZus1kBd6Yt02M4lR.FSwFHopQTwDEkOJRHZpp8HzP3OC2uBW			UTC	2025-07-23T08:44:35.671385Z	t	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1753186879	f	sY0L0Mf97iCjN8oLLtHXo0K8ujOUMcFI			f	2025-07-22 12:21:19	2025-07-23 08:44:35	\N	system
46	eb8fa644-57c4-4cd1-93ad-29271b6e33de	KgthnxPpar	0	individual	0	Test	User	Test User	Пользователь	test@test.com		f	f	$2y$12$ZV.O/T/TV4Yoc.hBVvj0u.VW/OvNdjTiTy7k/e.yZ0rlpQQwJ2c2S			UTC	2025-07-07T09:29:39.456524Z	t	ru	ru	Россия	Москва	/storage/uploads/avatars/avatar_46_1751880610.jpg	f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1751880557	f	mOB2DZfGJb5kZluOF2vQgq1YtXjDKbiW			f	2025-07-07 09:29:17	2025-07-07 09:30:10	\N	system
39	ed14bdb5-5b9d-4c62-b999-5c7ad5399631	qYL2PEFvMr	0	individual	0	Edward McCain		Edward McCain	CEO	edmccain0@gmail.com	+998 50 708 33 86	f	f	$2y$12$31lQa9ec7Lt5MbwRAY8pPuW7VgBU./JXVm12nQRZCSXIxkMUuvCp2			UTC+5	2025-07-21T04:14:08.336221Z	t	ru	ru	Узбекистан	Ташкент	/storage/uploads/avatars/avatar_39_1752643865.jpg	f	USD	0.0000000000	0.0000000000	0	0.0000000000	123456789	0	f		EM WA		0									0	0	f	t	t	t	t	t	f	f	f	t		1751549616	f	q641ydwdNcpZrqShID09vSKGW06Dadee			f	2025-07-03 13:33:36	2025-07-21 04:14:08	\N	system
52	4442c7fb-d338-44a6-9321-bb4bcb5b76ec	QwRWTc86G6	0	individual	0	Edward McCain		edmccain02@gmail.com	Пользователь	edmccain02@gmail.com	+998 50 708 33 86	f	f	$2y$12$x9E2L6EpXaAnDlMr.wOrtei94iQGtUKDRjPSvReWlQEkQADDaP.W.			UTC	2025-07-23T14:41:36.289778Z	t	ru	ru	Россия	Москва	/storage/uploads/avatars/avatar_52_1753174223.jpg	f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1752645203	f	P86tCfUOod9YS4jGlPfqDeEy2pEB0txk			f	2025-07-16 05:53:23	2025-07-29 09:13:00	"{\\"description\\":true,\\"country\\":false,\\"supplier\\":false,\\"article\\":true,\\"code\\":false,\\"external_code\\":false,\\"weight\\":false,\\"volume\\":false,\\"vat\\":false,\\"min_stock\\":false,\\"stock_type\\":false,\\"packing\\":false,\\"accounting_type\\":false,\\"traceable\\":false,\\"marking\\":false,\\"product_type\\":false,\\"barcode_type\\":false,\\"barcode\\":false,\\"cash_register_tax\\":false,\\"cash_register_type\\":false,\\"price\\":true,\\"categories\\":true,\\"unit\\":false,\\"category\\":true,\\"subcategory\\":true}"	user
57	2dea7727-2da0-4bc4-8802-cc55bdb14747	cLZ4HBiFU1	0	individual	0	edmccain00000@gmail.com		edmccain00000@gmail.com	Пользователь	edmccain00000@gmail.com		f	f	$2y$12$BNApCpQS3AILZelc3CUJkO6KdpmD2YBW500wvRe7Yhi3fz4E0qpR.			UTC	2025-07-23T06:39:58.334169Z	t	ru	ru	Россия	Москва		f	USD	0.0000000000	0.0000000000	0	0.0000000000		0	f				0									0	0	f	t	t	t	t	t	f	f	f	t		1753250886	f	Ti7EgmE3mZpoElofAzDINU32s92DiR5q			f	2025-07-23 06:08:06	2025-07-23 06:39:58	\N	system
55	7619fd20-0d3e-4472-9ea7-d24ff9d08858	Yf3TCykReC	0	individual	0	WINDCOM		WINDCOM	Пользователь	artel.2025@mail.ru	+998 97 400 11 13	f	f	$2y$12$BEhNXlmwMjKaSxCM4zOsDu6YjfjizQDGzH.xEHn4C0laCO9JhbQ22			UTC+5	2025-07-22T10:32:55.553701Z	f	ru	ru	Узбекистан	Ташкент	/storage/uploads/avatars/avatar_55_1753181655.jpg	f	UZS	0.0000000000	0.0000000000	0	0.0000000000	36787665	0	f		WINDCOM		0									0	0	f	t	t	t	t	t	f	f	f	t		1753180375	f	6mM6v9mqMYLlFs63gJnDieRxZkJMfmbl			f	2025-07-22 10:32:55	2025-07-22 10:55:43	\N	system
45	84067eab-6eca-4ff0-bee4-e3fec2dd68bc	z94FKgl78r	0	individual	0	artel.2008@mail.ru		artel.2008@mail.ru	Пользователь	artel.2008@mail.ru	+998 97 400 11 13	f	f	$2y$12$R.Viwdi0HuDlVP4yDLjf5Ow8q1CWOwWpi/ROYRKnlptwAeGl8ZG7S			UTC+5	2025-07-23T07:21:37.643858Z	t	ru	ru	Узбекистан	Ташкент	/storage/uploads/avatars/avatar_45_1753360864.jpg	f	UZS	0.0000000000	0.0000000000	0	0.0000000000	310535927	0	f		WINDCOM		0									0	0	f	t	t	t	t	t	f	f	f	t		1751559869	f	THdOzhQYO2uEcQP3mBAIDoI14jJAG76u			f	2025-07-03 16:24:29	2025-07-25 17:09:54	"{\\"description\\":false,\\"country\\":false,\\"supplier\\":false,\\"article\\":false,\\"code\\":false,\\"external_code\\":true,\\"weight\\":true,\\"volume\\":false,\\"vat\\":false,\\"min_stock\\":true,\\"stock_type\\":false,\\"packing\\":false,\\"accounting_type\\":false,\\"traceable\\":false,\\"marking\\":false,\\"product_type\\":false,\\"barcode_type\\":false,\\"barcode\\":false,\\"cash_register_tax\\":false,\\"cash_register_type\\":false}"	system
47	6ea761b4-5c0b-4a9f-ba63-4553e3fc4065	q6kw9VHGV2	0	individual	0	Edward McCain		edmccain@yandex.ru	Пользователь	edmccain@yandex.ru	+998 50 708 33 86	f	f	$2y$12$Gc0M3Fm2dEIK0RXW8RwvPubGE97YWvVQOVYc1Fb/jJn5J1j94Xh3q			UTC+3	2025-07-29T05:46:24.104962Z	t	ru	ru	Россия	Москва	/storage/uploads/avatars/avatar_47_1752493139.jpg	f	UZS	0.0000000000	0.0000000000	0	0.0000000000	987654321	0	f		McCorp		0									0	0	f	t	t	t	t	t	f	f	f	t		1751891221	f	np4lXg7xlzi2n7wWs7Grr3Pk0YDWGyCK			f	2025-07-07 12:27:01	2025-07-29 11:34:43	"{\\"description\\":false,\\"country\\":false,\\"supplier\\":false,\\"article\\":false,\\"code\\":false,\\"external_code\\":false,\\"weight\\":false,\\"volume\\":false,\\"vat\\":false,\\"min_stock\\":false,\\"stock_type\\":false,\\"packing\\":false,\\"accounting_type\\":false,\\"traceable\\":false,\\"marking\\":false,\\"product_type\\":false,\\"barcode_type\\":false,\\"barcode\\":false,\\"cash_register_tax\\":false,\\"cash_register_type\\":false,\\"price\\":true,\\"categories\\":true,\\"unit\\":true,\\"category\\":true,\\"subcategory\\":true}"	system
\.


--
-- Data for Name: warehouses; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.warehouses (id, user_id, name, address, created_at, updated_at) FROM stdin;
1	47	Главный склад	Луна, обратная сторона, кратер №217	2025-07-09 05:36:16	2025-07-09 05:36:16
2	39	Главный склад	Луна, кратер №96	2025-07-09 07:13:00	2025-07-09 07:13:00
3	47	Запасной склад	Марс	2025-07-09 09:00:54	2025-07-09 09:00:54
7	45	ЯНГАБОД СКЛАД	\N	2025-07-09 16:00:53	2025-07-09 16:00:53
8	45	ИПАДРОМ СКЛАД	\N	2025-07-14 12:19:26	2025-07-14 12:19:26
9	52	Склад 1	Москва	2025-07-16 06:01:22	2025-07-16 06:01:22
10	55	ИПАДРОМ	\N	2025-07-22 11:00:38	2025-07-22 11:00:38
11	55	ЧОРСУ	\N	2025-07-22 11:00:47	2025-07-22 11:00:47
12	55	Куйлюк	\N	2025-07-22 11:00:55	2025-07-22 11:00:55
13	56	1	1	2025-07-22 12:21:44	2025-07-22 12:21:44
14	57	a1	a1	2025-07-23 06:40:40	2025-07-23 06:40:40
15	50	test	fidjosiondf	2025-07-27 18:56:15	2025-07-27 18:56:15
16	52	222	2	2025-07-28 03:59:54	2025-07-28 03:59:54
\.


--
-- Data for Name: write_off_files; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.write_off_files (id, write_off_id, filename, size_mb, uploaded_at, employee, file_url) FROM stdin;
14	24	товары_2025-07-27_23-50-15.xlsx	0.00	2025-07-27 18:58:40		https://api.b2bstorage.ru/storage/uploads/write_offs/write_off_688676dc9b99a9.94615805.xlsx
\.


--
-- Data for Name: write_off_positions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.write_off_positions (id, write_off_id, name, code, barcode, article, quantity, balance, price, amount, reason, gtd, rnpt, country, product_id, created_at, updated_at) FROM stdin;
22	15	Удобрение азотное NPK	\N	\N	UDOB-NPK-004	10.000	0.000	0.00	0.00	Недостача по инвентаризации №21	\N	\N	\N	105	2025-07-23 09:52:35	2025-07-23 09:52:35
24	18	Краска акриловая белая	\N	\N	KRASKA-ACR-003	10.000	0.000	0.00	0.00	Недостача по инвентаризации №44	\N	\N	\N	133	2025-07-26 08:05:00	2025-07-26 08:05:00
25	19	Клей ПВА универсальный	\N	\N	KLEI-PVA-002	5.000	0.000	1000.00	5000.00	Недостача по инвентаризации №46	\N	\N	\N	120	2025-07-26 08:52:22	2025-07-26 08:52:22
27	21	Клей ПВА универсальный	\N	\N	KLEI-PVA-002	5.000	0.000	1000.00	5000.00	Недостача по инвентаризации №50	\N	\N	\N	120	2025-07-26 11:48:00	2025-07-26 11:48:00
28	22	Удобрение азотное NPK	\N	\N	UDOB-NPK-004	100.000	0.000	1000.00	100000.00	Недостача по инвентаризации №51	\N	\N	\N	122	2025-07-26 11:49:31	2025-07-26 11:49:31
29	22	Провод медный ВВГнг 3x2.5	\N	\N	PROV-VVG-005	100.000	0.000	100.00	10000.00	Недостача по инвентаризации №51	\N	\N	\N	123	2025-07-26 11:49:31	2025-07-26 11:49:31
30	22	Косметика увлажняющий крем	\N	\N	KOSM-CREM-006	100.000	0.000	500.00	50000.00	Недостача по инвентаризации №51	\N	\N	\N	124	2025-07-26 11:49:31	2025-07-26 11:49:31
\.


--
-- Data for Name: write_offs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.write_offs (id, number, date, organization, project, warehouse, status, is_posted, comment, total, overhead_costs, created_by, created_at, user_id, updated_at) FROM stdin;
23	222	2025-07-26 12:25:00	\N	-	1	posted	f	-	5900.00	0.00	Edward McCain	2025-07-26 12:26:05	47	2025-07-26 12:26:05
24	9998989	2025-07-27 18:55:00	\N	fdgdg	15	draft	f	rget43t3re	344.00	344.00	Rudolf7	2025-07-27 18:58:40	50	2025-07-27 18:58:40
11	1	2025-07-23 05:55:00	\N	\N	9	posted	f	\N	0.00	0.00	edmccain02@gmail.com	2025-07-23 06:02:23	52	2025-07-23 06:02:26
15	ИНВ-СПИ-21-23072025	2025-07-23 09:52:35	Автоматическое списание по инвентаризации: 11111	Инвентаризация	13	posted	t	Автоматическое списание по инвентаризации №21	0.00	0.00	edmccain0333@gmail.com	2025-07-23 09:52:35	56	2025-07-23 09:52:35
18	ИНВ-СПИ-44-26072025	2025-07-26 08:04:59	Автоматическое списание по инвентаризации: тест авто списаний и оприходований	Инвентаризация	9	posted	t	Автоматическое списание по инвентаризации №44	0.00	0.00	Edward McCain	2025-07-26 08:04:59	52	2025-07-26 08:04:59
19	ИНВ-СПИ-46-26072025	2025-07-26 08:52:22	Автоматическое списание по инвентаризации: test	Инвентаризация	1	posted	t	Автоматическое списание по инвентаризации №46	5000.00	0.00	Edward McCain	2025-07-26 08:52:22	47	2025-07-26 08:52:22
20	2321	2025-07-26 08:57:00	\N	\N	3	posted	f	\N	5100.00	100.00	Edward McCain	2025-07-26 08:58:06	47	2025-07-26 08:58:06
21	ИНВ-СПИ-50-26072025	2025-07-26 11:48:00	Автоматическое списание по инвентаризации: lkjlk	Инвентаризация	1	posted	t	Автоматическое списание по инвентаризации №50	5000.00	0.00	Edward McCain	2025-07-26 11:48:00	47	2025-07-26 11:48:00
22	ИНВ-СПИ-51-26072025	2025-07-26 11:49:31	Автоматическое списание по инвентаризации: 111	Инвентаризация	1	posted	t	Автоматическое списание по инвентаризации №51	160000.00	0.00	Edward McCain	2025-07-26 11:49:31	47	2025-07-26 11:49:31
\.


--
-- Name: currencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.currencies_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: inventories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.inventories_id_seq', 53, true);


--
-- Name: inventory_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.inventory_files_id_seq', 10, true);


--
-- Name: inventory_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.inventory_items_id_seq', 161, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 8, true);


--
-- Name: modifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.modifications_id_seq', 1, false);


--
-- Name: notifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.notifications_id_seq', 12, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 102, true);


--
-- Name: product_balances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_balances_id_seq', 74, true);


--
-- Name: product_fields_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_fields_id_seq', 6, true);


--
-- Name: product_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_images_id_seq', 66, true);


--
-- Name: product_operations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_operations_id_seq', 32, true);


--
-- Name: product_transfer_positions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_transfer_positions_id_seq', 6, true);


--
-- Name: product_transfers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.product_transfers_id_seq', 6, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.products_id_seq', 1, false);


--
-- Name: products_sklad_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.products_sklad_id_seq', 151, true);


--
-- Name: receipt_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.receipt_files_id_seq', 1, true);


--
-- Name: receipt_positions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.receipt_positions_id_seq', 57, true);


--
-- Name: receipt_tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.receipt_tasks_id_seq', 1, false);


--
-- Name: receipts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.receipts_id_seq', 27, true);


--
-- Name: user_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_categories_id_seq', 11, true);


--
-- Name: user_subcategories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_subcategories_id_seq', 13, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 57, true);


--
-- Name: warehouses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.warehouses_id_seq', 16, true);


--
-- Name: write_off_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.write_off_files_id_seq', 14, true);


--
-- Name: write_off_positions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.write_off_positions_id_seq', 33, true);


--
-- Name: write_offs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.write_offs_id_seq', 24, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: currencies currencies_currency_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.currencies
    ADD CONSTRAINT currencies_currency_id_key UNIQUE (currency_id);


--
-- Name: currencies currencies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.currencies
    ADD CONSTRAINT currencies_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: inventories inventories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventories
    ADD CONSTRAINT inventories_pkey PRIMARY KEY (id);


--
-- Name: inventory_files inventory_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_files
    ADD CONSTRAINT inventory_files_pkey PRIMARY KEY (id);


--
-- Name: inventory_items inventory_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_items
    ADD CONSTRAINT inventory_items_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: modifications modifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modifications
    ADD CONSTRAINT modifications_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: product_balances product_balances_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances
    ADD CONSTRAINT product_balances_pkey PRIMARY KEY (id);


--
-- Name: product_balances product_balances_product_id_warehouse_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances
    ADD CONSTRAINT product_balances_product_id_warehouse_id_key UNIQUE (product_id, warehouse_id);


--
-- Name: product_fields product_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_fields
    ADD CONSTRAINT product_fields_pkey PRIMARY KEY (id);


--
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- Name: product_operations product_operations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations
    ADD CONSTRAINT product_operations_pkey PRIMARY KEY (id);


--
-- Name: product_transfer_positions product_transfer_positions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfer_positions
    ADD CONSTRAINT product_transfer_positions_pkey PRIMARY KEY (id);


--
-- Name: product_transfers product_transfers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers
    ADD CONSTRAINT product_transfers_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products_sklad products_sklad_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products_sklad
    ADD CONSTRAINT products_sklad_pkey PRIMARY KEY (id);


--
-- Name: products products_vendore_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_vendore_key UNIQUE (vendore);


--
-- Name: receipt_files receipt_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_files
    ADD CONSTRAINT receipt_files_pkey PRIMARY KEY (id);


--
-- Name: receipt_positions receipt_positions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_positions
    ADD CONSTRAINT receipt_positions_pkey PRIMARY KEY (id);


--
-- Name: receipt_tasks receipt_tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_tasks
    ADD CONSTRAINT receipt_tasks_pkey PRIMARY KEY (id);


--
-- Name: receipts receipts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipts
    ADD CONSTRAINT receipts_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: subcategories subcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategories
    ADD CONSTRAINT subcategories_pkey PRIMARY KEY (id);


--
-- Name: user_categories user_categories_category_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_categories
    ADD CONSTRAINT user_categories_category_id_key UNIQUE (category_id);


--
-- Name: user_categories user_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_categories
    ADD CONSTRAINT user_categories_pkey PRIMARY KEY (id);


--
-- Name: user_subcategories user_subcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subcategories
    ADD CONSTRAINT user_subcategories_pkey PRIMARY KEY (id);


--
-- Name: user_subcategories user_subcategories_subcategory_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subcategories
    ADD CONSTRAINT user_subcategories_subcategory_id_key UNIQUE (subcategory_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_user_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_user_name_key UNIQUE (user_name);


--
-- Name: warehouses warehouses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.warehouses
    ADD CONSTRAINT warehouses_pkey PRIMARY KEY (id);


--
-- Name: write_off_files write_off_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_files
    ADD CONSTRAINT write_off_files_pkey PRIMARY KEY (id);


--
-- Name: write_off_positions write_off_positions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_positions
    ADD CONSTRAINT write_off_positions_pkey PRIMARY KEY (id);


--
-- Name: write_offs write_offs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_offs
    ADD CONSTRAINT write_offs_pkey PRIMARY KEY (id);


--
-- Name: idx_currencies_currency_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_currencies_currency_type ON public.currencies USING btree (currency_type);


--
-- Name: idx_currencies_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_currencies_date ON public.currencies USING btree (date);


--
-- Name: idx_inventories_created_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventories_created_at ON public.inventories USING btree (created_at);


--
-- Name: idx_inventories_created_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventories_created_by ON public.inventories USING btree (created_by);


--
-- Name: idx_inventories_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventories_status ON public.inventories USING btree (status);


--
-- Name: idx_inventories_warehouse_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventories_warehouse_id ON public.inventories USING btree (warehouse_id);


--
-- Name: idx_inventory_files_inventory_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_files_inventory_id ON public.inventory_files USING btree (inventory_id);


--
-- Name: idx_inventory_files_uploaded_by; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_files_uploaded_by ON public.inventory_files USING btree (uploaded_by);


--
-- Name: idx_inventory_items_inventory_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_items_inventory_id ON public.inventory_items USING btree (inventory_id);


--
-- Name: idx_inventory_items_photo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_items_photo ON public.inventory_items USING btree (photo);


--
-- Name: idx_inventory_items_product_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_inventory_items_product_id ON public.inventory_items USING btree (product_id);


--
-- Name: idx_product_balances_product_warehouse; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_balances_product_warehouse ON public.product_balances USING btree (product_id, warehouse_id);


--
-- Name: idx_product_operations_product_warehouse; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_operations_product_warehouse ON public.product_operations USING btree (product_id, warehouse_id);


--
-- Name: idx_product_operations_reference; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_operations_reference ON public.product_operations USING btree (reference_type, reference_id);


--
-- Name: idx_product_operations_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_operations_type ON public.product_operations USING btree (operation_type);


--
-- Name: idx_product_transfer_positions_product_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfer_positions_product_id ON public.product_transfer_positions USING btree (product_id);


--
-- Name: idx_product_transfer_positions_transfer_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfer_positions_transfer_id ON public.product_transfer_positions USING btree (transfer_id);


--
-- Name: idx_product_transfers_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfers_date ON public.product_transfers USING btree (transfer_date);


--
-- Name: idx_product_transfers_from_warehouse; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfers_from_warehouse ON public.product_transfers USING btree (from_warehouse_id);


--
-- Name: idx_product_transfers_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfers_status ON public.product_transfers USING btree (status);


--
-- Name: idx_product_transfers_to_warehouse; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_product_transfers_to_warehouse ON public.product_transfers USING btree (to_warehouse_id);


--
-- Name: idx_products_sklad_price; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_products_sklad_price ON public.products_sklad USING btree (price);


--
-- Name: idx_products_sklad_start_count; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_products_sklad_start_count ON public.products_sklad USING btree (start_count);


--
-- Name: idx_users_currency; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_users_currency ON public.users USING btree (currency);


--
-- Name: idx_warehouses_user_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_warehouses_user_id ON public.warehouses USING btree (user_id);


--
-- Name: idx_write_off_files_write_off_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_off_files_write_off_id ON public.write_off_files USING btree (write_off_id);


--
-- Name: idx_write_off_positions_write_off_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_off_positions_write_off_id ON public.write_off_positions USING btree (write_off_id);


--
-- Name: idx_write_offs_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_offs_date ON public.write_offs USING btree (date);


--
-- Name: idx_write_offs_status; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_offs_status ON public.write_offs USING btree (status);


--
-- Name: idx_write_offs_user_id; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_offs_user_id ON public.write_offs USING btree (user_id);


--
-- Name: idx_write_offs_warehouse; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_write_offs_warehouse ON public.write_offs USING btree (warehouse);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: users_user_id_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX users_user_id_unique ON public.users USING btree (user_id);


--
-- Name: inventory_files update_inventory_files_updated_at; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER update_inventory_files_updated_at BEFORE UPDATE ON public.inventory_files FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: product_balances fk_product_balances_product_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances
    ADD CONSTRAINT fk_product_balances_product_id FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_balances fk_product_balances_warehouse_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances
    ADD CONSTRAINT fk_product_balances_warehouse_id FOREIGN KEY (warehouse_id) REFERENCES public.warehouses(id) ON DELETE CASCADE;


--
-- Name: product_operations fk_product_operations_created_by; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations
    ADD CONSTRAINT fk_product_operations_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: product_operations fk_product_operations_product_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations
    ADD CONSTRAINT fk_product_operations_product_id FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_operations fk_product_operations_warehouse_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations
    ADD CONSTRAINT fk_product_operations_warehouse_id FOREIGN KEY (warehouse_id) REFERENCES public.warehouses(id) ON DELETE CASCADE;


--
-- Name: product_transfer_positions fk_product_transfer_positions_product_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfer_positions
    ADD CONSTRAINT fk_product_transfer_positions_product_id FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: products_sklad fk_products_sklad_warehouse_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products_sklad
    ADD CONSTRAINT fk_products_sklad_warehouse_id FOREIGN KEY (warehouse_id) REFERENCES public.warehouses(id) ON DELETE SET NULL;


--
-- Name: products fk_products_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT fk_products_user FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- Name: product_fields fk_user; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_fields
    ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: inventories inventories_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventories
    ADD CONSTRAINT inventories_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: inventories inventories_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventories
    ADD CONSTRAINT inventories_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES public.warehouses(id) ON DELETE CASCADE;


--
-- Name: inventory_files inventory_files_inventory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_files
    ADD CONSTRAINT inventory_files_inventory_id_fkey FOREIGN KEY (inventory_id) REFERENCES public.inventories(id) ON DELETE CASCADE;


--
-- Name: inventory_files inventory_files_uploaded_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_files
    ADD CONSTRAINT inventory_files_uploaded_by_fkey FOREIGN KEY (uploaded_by) REFERENCES public.users(id);


--
-- Name: inventory_items inventory_items_inventory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_items
    ADD CONSTRAINT inventory_items_inventory_id_fkey FOREIGN KEY (inventory_id) REFERENCES public.inventories(id) ON DELETE CASCADE;


--
-- Name: inventory_items inventory_items_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inventory_items
    ADD CONSTRAINT inventory_items_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: notifications notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: product_balances product_balances_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_balances
    ADD CONSTRAINT product_balances_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_images product_images_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_operations product_operations_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_operations
    ADD CONSTRAINT product_operations_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_transfer_positions product_transfer_positions_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfer_positions
    ADD CONSTRAINT product_transfer_positions_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: product_transfer_positions product_transfer_positions_transfer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfer_positions
    ADD CONSTRAINT product_transfer_positions_transfer_id_fkey FOREIGN KEY (transfer_id) REFERENCES public.product_transfers(id) ON DELETE CASCADE;


--
-- Name: product_transfers product_transfers_completed_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers
    ADD CONSTRAINT product_transfers_completed_by_fkey FOREIGN KEY (completed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: product_transfers product_transfers_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers
    ADD CONSTRAINT product_transfers_created_by_fkey FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: product_transfers product_transfers_from_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers
    ADD CONSTRAINT product_transfers_from_warehouse_id_fkey FOREIGN KEY (from_warehouse_id) REFERENCES public.warehouses(id) ON DELETE RESTRICT;


--
-- Name: product_transfers product_transfers_to_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.product_transfers
    ADD CONSTRAINT product_transfers_to_warehouse_id_fkey FOREIGN KEY (to_warehouse_id) REFERENCES public.warehouses(id) ON DELETE RESTRICT;


--
-- Name: products_sklad products_sklad_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.products_sklad
    ADD CONSTRAINT products_sklad_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: receipt_files receipt_files_receipt_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_files
    ADD CONSTRAINT receipt_files_receipt_id_fkey FOREIGN KEY (receipt_id) REFERENCES public.receipts(id) ON DELETE CASCADE;


--
-- Name: receipt_positions receipt_positions_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_positions
    ADD CONSTRAINT receipt_positions_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: receipt_positions receipt_positions_receipt_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_positions
    ADD CONSTRAINT receipt_positions_receipt_id_fkey FOREIGN KEY (receipt_id) REFERENCES public.receipts(id) ON DELETE CASCADE;


--
-- Name: receipt_tasks receipt_tasks_receipt_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipt_tasks
    ADD CONSTRAINT receipt_tasks_receipt_id_fkey FOREIGN KEY (receipt_id) REFERENCES public.receipts(id) ON DELETE CASCADE;


--
-- Name: receipts receipts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.receipts
    ADD CONSTRAINT receipts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_categories user_categories_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_categories
    ADD CONSTRAINT user_categories_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_subcategories user_subcategories_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subcategories
    ADD CONSTRAINT user_subcategories_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.user_categories(category_id) ON DELETE CASCADE;


--
-- Name: user_subcategories user_subcategories_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subcategories
    ADD CONSTRAINT user_subcategories_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: warehouses warehouses_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.warehouses
    ADD CONSTRAINT warehouses_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: write_off_files write_off_files_write_off_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_files
    ADD CONSTRAINT write_off_files_write_off_id_fkey FOREIGN KEY (write_off_id) REFERENCES public.write_offs(id) ON DELETE CASCADE;


--
-- Name: write_off_positions write_off_positions_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_positions
    ADD CONSTRAINT write_off_positions_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products_sklad(id) ON DELETE CASCADE;


--
-- Name: write_off_positions write_off_positions_write_off_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_off_positions
    ADD CONSTRAINT write_off_positions_write_off_id_fkey FOREIGN KEY (write_off_id) REFERENCES public.write_offs(id) ON DELETE CASCADE;


--
-- Name: write_offs write_offs_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.write_offs
    ADD CONSTRAINT write_offs_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- PostgreSQL database dump complete
--

