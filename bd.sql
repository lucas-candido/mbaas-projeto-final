--
-- PostgreSQL database dump
--

-- Dumped from database version 14.5 (Homebrew)
-- Dumped by pg_dump version 14.4

-- Started on 2022-10-22 13:35:19 -03

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 24577)
-- Name: dblink; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS dblink WITH SCHEMA public;


--
-- TOC entry 3758 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION dblink; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION dblink IS 'connect to other PostgreSQL databases from within a database';


--
-- TOC entry 3 (class 3079 OID 24623)
-- Name: unaccent; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;


--
-- TOC entry 3759 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION unaccent; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION unaccent IS 'text search dictionary that removes accents';


--
-- TOC entry 287 (class 1255 OID 24631)
-- Name: ft_controla_inativos(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.ft_controla_inativos() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN

/*
 * Função de trigger que inativa produtos sem estoque e reativa quando voltam a ter
 * Autor: Lucas Cândido dos Santos.
 * Data: 20/10/2022
 * Última Modificação: 20/10/2022 por Lucas Cândido dos Santos.
 */

 IF new.quantidade <= 0 THEN
  new.inativo = TRUE;
 ELSE
  new.inativo = FALSE;
 END IF;

 
RETURN NEW;
END;
$$;


ALTER FUNCTION public.ft_controla_inativos() OWNER TO postgres;

--
-- TOC entry 286 (class 1255 OID 24632)
-- Name: tira_acentos(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.tira_acentos(ptexto text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
begin
 return unaccent(ptexto);
end;
$$;


ALTER FUNCTION public.tira_acentos(ptexto text) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 212 (class 1259 OID 24633)
-- Name: admins; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.admins (
    id integer NOT NULL,
    email character varying(255),
    name character varying(255),
    password character varying(255),
    inativo boolean DEFAULT false,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.admins OWNER TO postgres;

--
-- TOC entry 213 (class 1259 OID 24641)
-- Name: admins_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.admins_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.admins_id_seq OWNER TO postgres;

--
-- TOC entry 3760 (class 0 OID 0)
-- Dependencies: 213
-- Name: admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.admins_id_seq OWNED BY public.admins.id;


--
-- TOC entry 229 (class 1259 OID 24972)
-- Name: categorias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.categorias (
    id integer NOT NULL,
    nome character varying(50),
    pai integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    imagem text,
    principal boolean DEFAULT false
);


ALTER TABLE public.categorias OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 24971)
-- Name: categorias_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.categorias_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.categorias_id_seq OWNER TO postgres;

--
-- TOC entry 3761 (class 0 OID 0)
-- Dependencies: 228
-- Name: categorias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.categorias_id_seq OWNED BY public.categorias.id;


--
-- TOC entry 214 (class 1259 OID 24661)
-- Name: familias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.familias (
    id bigint NOT NULL,
    nome character varying(50) NOT NULL,
    pai integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    imagem text,
    principal boolean DEFAULT false
);


ALTER TABLE public.familias OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 24667)
-- Name: familias_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.familias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.familias_id_seq OWNER TO postgres;

--
-- TOC entry 3762 (class 0 OID 0)
-- Dependencies: 215
-- Name: familias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.familias_id_seq OWNED BY public.familias.id;


--
-- TOC entry 216 (class 1259 OID 24668)
-- Name: favoritos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.favoritos (
    id integer NOT NULL,
    user_id bigint,
    produto_id bigint,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.favoritos OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 24673)
-- Name: favoritos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.favoritos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.favoritos_id_seq OWNER TO postgres;

--
-- TOC entry 3763 (class 0 OID 0)
-- Dependencies: 217
-- Name: favoritos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.favoritos_id_seq OWNED BY public.favoritos.id;


--
-- TOC entry 218 (class 1259 OID 24693)
-- Name: item_pedidos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.item_pedidos (
    id bigint NOT NULL,
    pedido_id bigint NOT NULL,
    produto_id integer NOT NULL,
    quantidade double precision NOT NULL,
    unitario double precision NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.item_pedidos OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 24696)
-- Name: item_pedidos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.item_pedidos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.item_pedidos_id_seq OWNER TO postgres;

--
-- TOC entry 3764 (class 0 OID 0)
-- Dependencies: 219
-- Name: item_pedidos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.item_pedidos_id_seq OWNED BY public.item_pedidos.id;


--
-- TOC entry 220 (class 1259 OID 24697)
-- Name: marcas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.marcas (
    id bigint NOT NULL,
    nome character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    pai integer,
    imagem text,
    principal boolean DEFAULT false
);


ALTER TABLE public.marcas OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 24703)
-- Name: marcas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.marcas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.marcas_id_seq OWNER TO postgres;

--
-- TOC entry 3765 (class 0 OID 0)
-- Dependencies: 221
-- Name: marcas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.marcas_id_seq OWNED BY public.marcas.id;


--
-- TOC entry 222 (class 1259 OID 24714)
-- Name: pedidos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pedidos (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    pagamento integer,
    obs text,
    parcelas integer,
    status integer NOT NULL,
    motivocancel text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cancelusu boolean DEFAULT false,
    cancelempresa boolean DEFAULT false,
    vrfrete numeric(18,5) DEFAULT 0.00
);


ALTER TABLE public.pedidos OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 24723)
-- Name: pedidos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pedidos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pedidos_id_seq OWNER TO postgres;

--
-- TOC entry 3766 (class 0 OID 0)
-- Dependencies: 223
-- Name: pedidos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pedidos_id_seq OWNED BY public.pedidos.id;


--
-- TOC entry 224 (class 1259 OID 24724)
-- Name: produtos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.produtos (
    id bigint NOT NULL,
    produto character varying(20) NOT NULL,
    nome character varying(255) NOT NULL,
    quantidade double precision NOT NULL,
    preco double precision NOT NULL,
    tamanho character varying(10),
    cor character varying(50),
    inativo boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    imagem character varying(255),
    destaque boolean DEFAULT false,
    marca_id integer,
    preco_promocional double precision,
    descricao text,
    categoria_id integer
);


ALTER TABLE public.produtos OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 24731)
-- Name: produtos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.produtos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.produtos_id_seq OWNER TO postgres;

--
-- TOC entry 3767 (class 0 OID 0)
-- Dependencies: 225
-- Name: produtos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.produtos_id_seq OWNED BY public.produtos.id;


--
-- TOC entry 226 (class 1259 OID 24737)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cpfcnpj character varying(14),
    endereco character varying(40),
    numeroendereco character varying(10),
    bairro character varying(30),
    cidade character varying(255),
    estado character varying(2),
    cep character varying(8),
    telefone character varying(15),
    CONSTRAINT chk_pessoa CHECK (
CASE
    WHEN (length((cpfcnpj)::text) > 11) THEN (length((cpfcnpj)::text) = 14)
    ELSE (length((cpfcnpj)::text) = 11)
END)
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 24743)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 3768 (class 0 OID 0)
-- Dependencies: 227
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3543 (class 2604 OID 24744)
-- Name: admins id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admins ALTER COLUMN id SET DEFAULT nextval('public.admins_id_seq'::regclass);


--
-- TOC entry 3561 (class 2604 OID 24975)
-- Name: categorias id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias ALTER COLUMN id SET DEFAULT nextval('public.categorias_id_seq'::regclass);


--
-- TOC entry 3545 (class 2604 OID 24748)
-- Name: familias id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familias ALTER COLUMN id SET DEFAULT nextval('public.familias_id_seq'::regclass);


--
-- TOC entry 3548 (class 2604 OID 24749)
-- Name: favoritos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos ALTER COLUMN id SET DEFAULT nextval('public.favoritos_id_seq'::regclass);


--
-- TOC entry 3549 (class 2604 OID 24753)
-- Name: item_pedidos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_pedidos ALTER COLUMN id SET DEFAULT nextval('public.item_pedidos_id_seq'::regclass);


--
-- TOC entry 3551 (class 2604 OID 24754)
-- Name: marcas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.marcas ALTER COLUMN id SET DEFAULT nextval('public.marcas_id_seq'::regclass);


--
-- TOC entry 3555 (class 2604 OID 24756)
-- Name: pedidos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pedidos ALTER COLUMN id SET DEFAULT nextval('public.pedidos_id_seq'::regclass);


--
-- TOC entry 3558 (class 2604 OID 24757)
-- Name: produtos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produtos ALTER COLUMN id SET DEFAULT nextval('public.produtos_id_seq'::regclass);


--
-- TOC entry 3559 (class 2604 OID 24758)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3735 (class 0 OID 24633)
-- Dependencies: 212
-- Data for Name: admins; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.admins (id, email, name, password, inativo, created_at, updated_at) FROM stdin;
3	lucas@email.com	Lucas Cândido dos Santos	$2y$10$uAmJC/jKdBFI6zzYb3twmObUrPPB5Jj3Ysq0VJCVzABYD.BlbOdZi	f	2022-10-20 23:20:45	2022-10-20 23:21:32
4	juliano@email.com	Juliano Editado	$2y$10$7YAZW2ICqFqx0CNKLpnm..vhzbQftWgtru6mbxM4ZWB4QZmUi5QFa	f	2022-10-22 14:33:05	2022-10-22 14:33:46
5	daniel.nicolas.dossantos@moderna.com.br	Daniel Nicolas dos Santos	$2y$10$TdnesOkU.iYWj/vW2zae1uG9v5fWJg9.AuvmS1tqENDnUMwPIJdeC	f	2022-10-22 15:44:59	2022-10-22 15:44:59
\.


--
-- TOC entry 3752 (class 0 OID 24972)
-- Dependencies: 229
-- Data for Name: categorias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.categorias (id, nome, pai, created_at, updated_at, imagem, principal) FROM stdin;
1	Smartphones	\N	2022-10-21 21:14:10	2022-10-21 21:14:10	\N	t
2	Computadores	\N	2022-10-21 21:28:02	2022-10-21 21:29:39	google.com	f
\.


--
-- TOC entry 3737 (class 0 OID 24661)
-- Dependencies: 214
-- Data for Name: familias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.familias (id, nome, pai, created_at, updated_at, imagem, principal) FROM stdin;
\.


--
-- TOC entry 3739 (class 0 OID 24668)
-- Dependencies: 216
-- Data for Name: favoritos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.favoritos (id, user_id, produto_id, created_at, updated_at) FROM stdin;
3	2	2	2022-10-21 22:10:36	2022-10-21 22:10:36
\.


--
-- TOC entry 3741 (class 0 OID 24693)
-- Dependencies: 218
-- Data for Name: item_pedidos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.item_pedidos (id, pedido_id, produto_id, quantidade, unitario, created_at, updated_at) FROM stdin;
10	2	2	1	7599	2022-10-21 22:32:09	2022-10-21 22:32:09
11	2	2	1	7599	2022-10-21 22:33:24	2022-10-21 22:33:24
12	3	2	1	5999	2022-10-22 14:54:45	2022-10-22 14:54:45
13	4	2	1	5999	2022-10-22 15:59:47	2022-10-22 15:59:47
\.


--
-- TOC entry 3743 (class 0 OID 24697)
-- Dependencies: 220
-- Data for Name: marcas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.marcas (id, nome, created_at, updated_at, pai, imagem, principal) FROM stdin;
3	Motorola	2022-10-21 21:38:25	2022-10-21 21:38:25	\N	\N	f
4	Apple	2022-10-21 21:39:22	2022-10-21 21:39:22	\N	\N	t
2	Samsung	2022-10-21 21:38:14	2022-10-21 21:41:51	\N	asdasdasd	t
\.


--
-- TOC entry 3745 (class 0 OID 24714)
-- Dependencies: 222
-- Data for Name: pedidos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pedidos (id, user_id, pagamento, obs, parcelas, status, motivocancel, created_at, updated_at, cancelusu, cancelempresa, vrfrete) FROM stdin;
2	2	1	Pedido de teste	12	0	\N	2022-10-21 22:18:35	2022-10-21 22:18:35	f	f	0.00000
3	3	1	Teste	10	0	\N	2022-10-22 14:53:03	2022-10-22 14:53:03	f	f	0.00000
4	4	1	Teste	10	1	\N	2022-10-22 15:58:46	2022-10-22 16:30:01	f	f	0.00000
\.


--
-- TOC entry 3747 (class 0 OID 24724)
-- Dependencies: 224
-- Data for Name: produtos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.produtos (id, produto, nome, quantidade, preco, tamanho, cor, inativo, created_at, updated_at, imagem, destaque, marca_id, preco_promocional, descricao, categoria_id) FROM stdin;
4	SKU123	iPhone 14	100	9499	Pro	Roxo Profundo	f	2022-10-21 21:58:07	2022-10-21 21:58:07	\N	t	4	9499	Novo iPhone 14 Pro bla bla bla	1
5	SKU123	iPhone 14	100	10499	Pro Max	Roxo Profundo	f	2022-10-21 21:58:40	2022-10-21 21:58:40	\N	t	4	10499	Novo iPhone 14 Pro Max bla bla bla	1
3	SKU124	iPhone 14	100	8599	Plus	Product Red	f	2022-10-21 21:55:39	2022-10-21 22:04:45	\N	t	4	8599	Novo iPhone 14 Plus bla bla bla	1
6	SKU789	Galaxy S20	100	5000	Unico	Preto	f	2022-10-22 14:40:22	2022-10-22 14:40:22	\N	f	2	5000	adjaskdjaskdjask	1
7	cod123	Galaxy Tab	50	6000	Plus	Preto	f	2022-10-22 15:49:25	2022-10-22 15:49:25	\N	f	2	6000	dasdasdasdsada	1
2	SKU123	iPhone 14	96	7599	Padrão	Meia-noite	f	2022-10-21 21:53:56	2022-10-22 15:59:47	\N	t	4	7599	Novo iPhone 14 bla bla bla	1
\.


--
-- TOC entry 3749 (class 0 OID 24737)
-- Dependencies: 226
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, cpfcnpj, endereco, numeroendereco, bairro, cidade, estado, cep, telefone) FROM stdin;
2	Lucas	lucas@usuario.com	\N	$2y$10$xFzzjfcIW8tviLAwht2m4.grN/UomYzSchNItudYfqNiyK7PsugQG	\N	2022-10-21 21:44:22	2022-10-22 14:48:11	16983432568	Travessa Valdir Dias	593	Barro Vermelho	São Gonçalo	RJ	24415450	2128214978
3	Thiago Heitor Gael Pereira	thiagoheitorpereira@superig.com.br	\N	$2y$10$dMQX.jY3Z5zBlz6cUZFbFOiXqr6cr0iDsP/qlOa5QJFc42oWVB3m6	\N	2022-10-22 14:50:04	2022-10-22 14:50:04	95464955810	Rua Trigésima Primeira	557	Piracanã	Itaituba	PA	68180510	9329804934
4	Priscila Nascimento	priscila_nascimento@oralcamp.com.br	\N	$2y$10$qwmjnsxlX1ET0Y6/TQ.Fg.4gXwJ8Qoh5kHQDVT5GlIRzRkyhfryO2	\N	2022-10-22 15:53:55	2022-10-22 15:55:20	73536514588	Rua Senador Barros Leite	752	Jaraguá	Maceió	AL	57022280	8236025709
\.


--
-- TOC entry 3769 (class 0 OID 0)
-- Dependencies: 213
-- Name: admins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.admins_id_seq', 5, true);


--
-- TOC entry 3770 (class 0 OID 0)
-- Dependencies: 228
-- Name: categorias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.categorias_id_seq', 2, true);


--
-- TOC entry 3771 (class 0 OID 0)
-- Dependencies: 215
-- Name: familias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.familias_id_seq', 1, true);


--
-- TOC entry 3772 (class 0 OID 0)
-- Dependencies: 217
-- Name: favoritos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.favoritos_id_seq', 5, true);


--
-- TOC entry 3773 (class 0 OID 0)
-- Dependencies: 219
-- Name: item_pedidos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.item_pedidos_id_seq', 13, true);


--
-- TOC entry 3774 (class 0 OID 0)
-- Dependencies: 221
-- Name: marcas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.marcas_id_seq', 4, true);


--
-- TOC entry 3775 (class 0 OID 0)
-- Dependencies: 223
-- Name: pedidos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pedidos_id_seq', 4, true);


--
-- TOC entry 3776 (class 0 OID 0)
-- Dependencies: 225
-- Name: produtos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.produtos_id_seq', 7, true);


--
-- TOC entry 3777 (class 0 OID 0)
-- Dependencies: 227
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- TOC entry 3564 (class 2606 OID 24827)
-- Name: admins admins_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admins
    ADD CONSTRAINT admins_email_key UNIQUE (email);


--
-- TOC entry 3566 (class 2606 OID 24829)
-- Name: admins admins_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admins
    ADD CONSTRAINT admins_pkey PRIMARY KEY (id);


--
-- TOC entry 3588 (class 2606 OID 24980)
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (id);


--
-- TOC entry 3568 (class 2606 OID 24839)
-- Name: familias familias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.familias
    ADD CONSTRAINT familias_pkey PRIMARY KEY (id);


--
-- TOC entry 3570 (class 2606 OID 24841)
-- Name: favoritos favoritos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_pkey PRIMARY KEY (id);


--
-- TOC entry 3572 (class 2606 OID 24843)
-- Name: favoritos favoritos_user_id_produto_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_user_id_produto_id_key UNIQUE (user_id, produto_id);


--
-- TOC entry 3574 (class 2606 OID 24851)
-- Name: item_pedidos item_pedidos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_pedidos
    ADD CONSTRAINT item_pedidos_pkey PRIMARY KEY (id);


--
-- TOC entry 3576 (class 2606 OID 24855)
-- Name: marcas marcas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.marcas
    ADD CONSTRAINT marcas_pkey PRIMARY KEY (id);


--
-- TOC entry 3578 (class 2606 OID 24859)
-- Name: pedidos pedidos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_pkey PRIMARY KEY (id);


--
-- TOC entry 3580 (class 2606 OID 24863)
-- Name: produtos produtos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produtos
    ADD CONSTRAINT produtos_pkey PRIMARY KEY (id);


--
-- TOC entry 3582 (class 2606 OID 24867)
-- Name: users users_cpfcnpj; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_cpfcnpj UNIQUE (cpfcnpj);


--
-- TOC entry 3584 (class 2606 OID 24869)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 3586 (class 2606 OID 24871)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3595 (class 2620 OID 24872)
-- Name: produtos trig_controla_inativos; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trig_controla_inativos BEFORE INSERT OR UPDATE ON public.produtos FOR EACH ROW EXECUTE FUNCTION public.ft_controla_inativos();


--
-- TOC entry 3589 (class 2606 OID 24878)
-- Name: favoritos favoritos_produto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_produto_id_foreign FOREIGN KEY (produto_id) REFERENCES public.produtos(id);


--
-- TOC entry 3590 (class 2606 OID 24883)
-- Name: favoritos favoritos_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favoritos
    ADD CONSTRAINT favoritos_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3591 (class 2606 OID 24898)
-- Name: item_pedidos item_pedidos_pedido_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_pedidos
    ADD CONSTRAINT item_pedidos_pedido_id_foreign FOREIGN KEY (pedido_id) REFERENCES public.pedidos(id);


--
-- TOC entry 3592 (class 2606 OID 24903)
-- Name: item_pedidos item_pedidos_produto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.item_pedidos
    ADD CONSTRAINT item_pedidos_produto_id_foreign FOREIGN KEY (produto_id) REFERENCES public.produtos(id);


--
-- TOC entry 3593 (class 2606 OID 24908)
-- Name: pedidos pedidos_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pedidos
    ADD CONSTRAINT pedidos_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3594 (class 2606 OID 24918)
-- Name: produtos produtos_marca_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.produtos
    ADD CONSTRAINT produtos_marca_id_foreign FOREIGN KEY (marca_id) REFERENCES public.marcas(id);


-- Completed on 2022-10-22 13:35:19 -03

--
-- PostgreSQL database dump complete
--

