# Banco de Dados

## Objetivo

O MultiEcommerce utilizará um único banco de dados MySQL compartilhado entre todas as lojas (multi-tenant), com isolamento lógico através do campo `store_id`.

---

# Tecnologias

- MySQL 8
- InnoDB
- UTF8MB4
- Foreign Keys
- Soft Deletes
- Índices

---

# Filosofia

- Uma única base de dados.
- Uma única estrutura para todos os clientes.
- Sem tabelas EAV.
- Sem uso excessivo de campos JSON.
- Integridade referencial obrigatória.
- Performance acima de complexidade.

---

# Multi-Tenant

Cada registro pertencente a uma loja possuirá obrigatoriamente:

store_id

Toda consulta deverá respeitar o tenant ativo.

---

# Principais Tabelas

## Sistema

- stores
- store_domains
- users
- roles
- permissions

---

## Catálogo

- categories
- products
- product_variants
- brands
- media
- product_media

---

## Clientes

- customers
- customer_addresses

---

## Pedidos

- carts
- cart_items
- orders
- order_items

---

## Pagamentos

- payment_methods
- payments
- payment_transactions

---

## Entregas

- shipping_methods
- shipments

---

## CMS

- pages
- banners
- menus
- settings

---

## Auditoria

- activity_logs
- login_logs

---

## Estatísticas

- store_statistics

---

# Convenções

## Tabelas

Plural.

Exemplo:

products

orders

customers

---

## Chaves Primárias

id BIGINT UNSIGNED

---

## Chaves Estrangeiras

*_id

Exemplo:

store_id

product_id

customer_id

---

## Datas

created_at

updated_at

deleted_at

---

# Próxima Etapa

As migrações Laravel serão criadas individualmente, uma tabela por migration, mantendo versionamento completo do banco de dados.