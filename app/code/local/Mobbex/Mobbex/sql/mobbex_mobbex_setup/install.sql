CREATE TABLE IF NOT EXISTS DB_PREFIX_mobbex_transaction (
    `transaction_mobbex_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT(11) NOT NULL,
    `parent` TINYINT NOT NULL,
    `operation_type` TEXT NOT NULL,
    `payment_id` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `status_code` TEXT NOT NULL,
    `status_message` TEXT NOT NULL,
    `source_name` TEXT NOT NULL,
    `source_type` TEXT NOT NULL,
    `source_reference` TEXT NOT NULL,
    `source_number` TEXT NOT NULL,
    `source_expiration` TEXT NOT NULL,
    `source_installment` TEXT NOT NULL,
    `installment_name` TEXT NOT NULL,
    `installment_amount` TEXT NOT NULL,
    `installment_count` TEXT NOT NULL,
    `source_url` TEXT NOT NULL,
    `cardholder` TEXT NOT NULL,
    `entity_name` TEXT NOT NULL,
    `entity_uid` TEXT NOT NULL,
    `customer` TEXT NOT NULL,
    `checkout_uid` TEXT NOT NULL,
    `total` DECIMAL(18,2) NOT NULL,
    `currency` TEXT NOT NULL,
    `risk_analysis` TEXT NOT NULL,
    `data` TEXT NOT NULL,
    `created` TEXT NOT NULL,
    `updated` TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS DB_PREFIX_mobbex_customfield (
    `customfield_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `row_id` INT(11) NOT NULL,
    `object` TEXT NOT NULL,
    `field_name` TEXT NOT NULL,
    `data` TEXT NOT NULL
);