# DROP TABLE IF EXISTS event_images;
# DROP TABLE IF EXISTS event_image_types;
# DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS product_brands_product_events;
DROP TAbLE IF EXISTS product_bests_product_events;
DROP TABLE IF EXISTS product_events;
DROP TABLE IF EXISTS product_brands;
DROP TABLE IF EXISTS product_bests;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS brands;

CREATE TABLE brands
(
    id    INTEGER UNSIGNED NOT NULL UNIQUE PRIMARY KEY,
    slug  VARCHAR(255)     NOT NULL UNIQUE,
    name  VARCHAR(255)     NOT NULL UNIQUE,
    image VARCHAR(255)     NOT NULL UNIQUE,
    CONSTRAINT validate_url CHECK (image REGEXP '^(https?|ftp)://[-a-zA-Z0-9+&@#/%?=~_|!:,.;]*[-a-zA-Z0-9+&@#/%=~_|]$')
) engine = InnoDB;

CREATE TABLE categories
(
    id          INTEGER UNSIGNED NOT NULL UNIQUE PRIMARY KEY,
    slug        VARCHAR(255)     NOT NULL UNIQUE,
    name        VARCHAR(255)     NOT NULL UNIQUE,
    description VARCHAR(500)
) engine = InnoDB;

CREATE TABLE products
(
    id          INTEGER UNSIGNED NOT NULL UNIQUE,
    category_id INTEGER UNSIGNED NOT NULL,
    name        VARCHAR(255)     NOT NULL UNIQUE,
    description VARCHAR(500),
    image       VARCHAR(255),
    price       REAL UNSIGNED    NOT NULL,
    good_count  INTEGER UNSIGNED NOT NULL DEFAULT 0,
    view_count  INTEGER UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id, category_id),
    FOREIGN KEY (category_id) REFERENCES categories (id),
    CONSTRAINT validate_url CHECK (image REGEXP '^(https?|ftp)://[-a-zA-Z0-9+&@#/%?=~_|!:,.;]*[-a-zA-Z0-9+&@#/%=~_|]$')
) engine = InnoDB;

CREATE TABLE product_bests
(
    product_id INTEGER UNSIGNED NOT NULL UNIQUE,
    brand_id   INTEGER UNSIGNED NOT NULL,
    price      REAL UNSIGNED    NOT NULL,
    PRIMARY KEY (product_id, brand_id),
    FOREIGN KEY (product_id) REFERENCES products (id),
    FOREIGN KEY (brand_id) REFERENCES brands (id)
) engine = InnoDB;

CREATE TABLE product_brands
(
    product_id  INTEGER UNSIGNED NOT NULL,
    brand_id    INTEGER UNSIGNED NOT NULL,
    price       REAL UNSIGNED    NOT NULL,
    event_price REAL UNSIGNED,
    PRIMARY KEY (product_id, brand_id),
    FOREIGN KEY (product_id) REFERENCES products (id),
    FOREIGN KEY (brand_id) REFERENCES brands (id)
) engine = InnoDB;

CREATE TABLE product_events
(
    id          INTEGER UNSIGNED NOT NULL UNIQUE PRIMARY KEY,
    slug        VARCHAR(255)     NOT NULL UNIQUE,
    name        VARCHAR(255)     NOT NULL UNIQUE,
    description VARCHAR(500)
) engine = InnoDB;

CREATE TABLE product_brands_product_events
(
    product_id INTEGER UNSIGNED NOT NULL,
    brand_id   INTEGER UNSIGNED NOT NULL,
    event_id   INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, brand_id, event_id),
    FOREIGN KEY (product_id, brand_id) REFERENCES product_brands (product_id, brand_id),
    FOREIGN KEY (event_id) REFERENCES product_events (id)
) engine = InnoDB;

CREATE TABLE product_bests_product_events
(
    product_id INTEGER UNSIGNED NOT NULL,
    event_id   INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, event_id),
    FOREIGN KEY (product_id) REFERENCES product_bests (product_id),
    FOREIGN KEY (event_id) REFERENCES product_events (id)
) engine = InnoDB;

CREATE TABLE sessions
(
    id         CHAR(255) NOT NULL UNIQUE PRIMARY KEY,
    expired_at DATETIME  NOT NULL
)

# CREATE TABLE events
# (
#     id          INTEGER UNSIGNED NOT NULL UNIQUE,
#     brand_id    INTEGER UNSIGNED NOT NULL,
#     name        VARCHAR(255)     NOT NULL,
#     description VARCHAR(500),
#     start_at    DATE             NOT NULL,
#     end_at      DATE             NOT NULL,
#     good_count  INTEGER UNSIGNED NOT NULL DEFAULT 0,
#     view_count  INTEGER UNSIGNED NOT NULL DEFAULT 0,
#     PRIMARY KEY (id, brand_id),
#     FOREIGN KEY (brand_id) REFERENCES brands (id)
# ) engine = InnoDB;
#
# CREATE TABLE event_image_types
# (
#     name VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY
# ) engine = InnoDB;
#
# CREATE TABLE event_images
# (
#     event_id INTEGER UNSIGNED NOT NULL,
#     type     VARCHAR(255)     NOT NULL,
#     url      VARCHAR(255)     NOT NULL,
#     PRIMARY KEY (event_id, type, url),
#     FOREIGN KEY (event_id) REFERENCES events (id),
#     FOREIGN KEY (type) REFERENCES event_image_types (name),
#     CONSTRAINT validate_url CHECK (url REGEXP '^(https?|ftp)://[-a-zA-Z0-9+&@#/%?=~_|!:,.;]*[-a-zA-Z0-9+&@#/%=~_|]$')
# ) engine = InnoDB;
