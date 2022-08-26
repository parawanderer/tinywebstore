-- a basic seed & init for the databas

CREATE TABLE account(
	id INT auto_increment,
	username VARCHAR(255) UNIQUE NOT NULL,
	password_hash VARCHAR(255) NOT NULL,
	first_name VARCHAR(255) NOT NULL,
	last_name VARCHAR(255) NOT NULL,
	address VARCHAR(255) NOT NULL,
	created DATETIME NOT NULL,
    PRIMARY KEY(id)
);


INSERT INTO account VALUES(
	1,
    'bob@test.test',
    '$2y$10$YU8CZcSEKQQE9HFUixF9ouWFpVmkww3vomyVggViKvrPXG6Op9eJK',
    'Bob',
    'Jones',
    'Straatstraat 1, 1000 Brussel',
    '2022-08-22 12:45:56'
);

INSERT INTO account VALUES(
	2,
    'store@test.test',
    '$2y$10$noXphl0nxLyZ3rMnCGeV/.Hqy1UtFWsNsEXZE.OAwi9fZU9DvqEgq',
    'Will',
    'Smith',
    'Straatstraat 2, 1000 Brussel',
    '2022-08-22 12:55:23'
);

INSERT INTO account VALUES(
	3,
    'gsms@test.test',
    '$2y$10$noXphl0nxLyZ3rMnCGeV/.Hqy1UtFWsNsEXZE.OAwi9fZU9DvqEgq',
    'Bob',
    'Ross',
    'Straatstraat 3, 3500 Hasselt',
    '2022-08-23 12:55:23'
);

INSERT INTO account VALUES(
	4,
    'electronica@test.test',
    '$2y$10$noXphl0nxLyZ3rMnCGeV/.Hqy1UtFWsNsEXZE.OAwi9fZU9DvqEgq',
    'Bart',
    'Willems',
    'Eikenstraat 8, 3500 Hasselt',
    '2022-08-23 12:55:23'
);

INSERT INTO account VALUES(
	5,
    'tvs@test.test',
    '$2y$10$noXphl0nxLyZ3rMnCGeV/.Hqy1UtFWsNsEXZE.OAwi9fZU9DvqEgq',
    'Piet',
    'Piet',
    'Sinterklaasstraat 22, 3500 Hasselt',
    '2022-08-23 12:55:23'
);

CREATE TABLE shop(
	id INT auto_increment,
    user_id INT NOT NULL,
    `name` VARCHAR(255) UNIQUE NOT NULL,
    `description` TEXT,
    `phone_number` VARCHAR(255),
    `theme_color` VARCHAR(9),
    `font_color` VARCHAR(9),
    support_email VARCHAR(255),
    address VARCHAR(255),
    shop_logo_img VARCHAR(255) UNIQUE,
    shop_banner_img VARCHAR(255) UNIQUE,
    PRIMARY KEY (id),
    FOREIGN KEY(user_id) REFERENCES `account`(id)
);

INSERT INTO shop VALUES(
	1,
    2,
    'Kussen Winkel',
    'Zachte kussens voor iedereen!',
    '+32487777777',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

INSERT INTO shop VALUES(
	2,
    3,
    'GSMs Plus!',
    '<h2>GSMs Plus!</h2><p>De <b>beste</b> GSM winkel!</p>',
    '+32488455284',
    '#ffffff',
    '#1919ff',
    'support@gsm-plus.be',
    'Straatstraat 3, 3500 Hasselt',
    NULL,
    NULL
);

INSERT INTO shop VALUES(
	3,
    4,
    'Electronica.be',
    '<h2>Electronica.be</h2><p>De electronica winkel voor iedereen</p>',
    '+32484366896',
    NULL,
    NULL,
    'support@electronica.be',
    'Eikenstraat 8, 3500 Hasselt',
    NULL,
    NULL
);

INSERT INTO shop VALUES(
	4,
    5,
    'TVs Belgium XL',
    '<h2>TVs Belgium XL</h2><p>De tv winkel voor iedereen</p>',
    '+3244322169',
    NULL,
    NULL,
    'support@tvs-belgium.be',
    'Sinterklaasstraat 22, 3500 Hasselt',
    NULL,
    NULL
);

CREATE TABLE product(
	id INT AUTO_INCREMENT,
    shop_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    availability INT NOT NULL DEFAULT 0,
	main_media VARCHAR(255),
    `description` TEXT,
    PRIMARY KEY (id),
    foreign key (shop_id) REFERENCES shop(id)
);

CREATE INDEX shopindx ON product(shop_id);

CREATE TABLE shop_media(
	id VARCHAR(255),
    mimetype VARCHAR(30) NOT NULL,
    shop_id INT NOT NULL,
    created DATETIME NOT NULL,
    product_id INT,
    PRIMARY KEY(id),
    FOREIGN KEY(shop_id) REFERENCES shop(id),
    FOREIGN KEY (product_id) REFERENCES product(id)
);

CREATE INDEX shopindx ON shop_media (shop_id);
CREATE INDEX prodindx ON shop_media (product_id);

INSERT INTO product VALUES (
	1,
    1,
    "Hoofdkussen 'Slaap Lekker'",
    25.50,
    120,
	NULL,
    NULL
);

INSERT INTO product VALUES (
	2,
    1,
    "Hoofdkussen 'Royal'",
    36.49,
    80,
	NULL,
    NULL
);

INSERT INTO product VALUES (
	3,
    1,
    "Hoofdkussen voor kinderen 'Smurfen'",
    10.49,
    200,
	NULL,
    NULL
);

CREATE TABLE review(
	id INT AUTO_INCREMENT,
    author_id INT NOT NULL,
    product_id INT NOT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(255) NOT NULL,
    rating TINYINT NOT NULL DEFAULT 1,
    content TEXT NOT NULL,
    PRIMARY KEY (id),
    foreign key (author_id) REFERENCES account(id),
    foreign key (product_id) REFERENCES product(id)
);

CREATE INDEX authorIndx ON review(author_id);
CREATE INDEX productIndx ON review(product_id);

INSERT INTO review (id, author_id, product_id, `timestamp`, title, rating, content) VALUES (
	1,
    1,
    2,
    NOW(),
    'Mooi kussen',
    5,
    'Dit is een zeer goed kussen, ik slaap er top op. Ik raad het aan!'
);

INSERT INTO review (id, author_id, product_id, `timestamp`, title, rating, content) VALUES (
	2,
    2,
    2,
    NOW(),
    'goed kussen',
    4,
    'Ik heb dit kussen 2 weken geleden geleden besteld. Ze zeiden dat het zou aankomen in 2 dagen maar ik heb er meerdere dagen op moeten wachten.\n\nKussen slaapt lekker.'
);

CREATE TABLE watchlist(
	id INT auto_increment,
	user_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL, -- product may be deleted, so fallback on old product name in list in that case
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    foreign key (user_id) REFERENCES `account`(id)
);

CREATE INDEX userProductIndex ON watchlist(user_id, product_id);

INSERT INTO watchlist (user_id, product_id, product_name, created) VALUES(
	1, 
    1, 
    "Hoofdkussen 'Slaap Lekker'",
    NOW()
);

INSERT INTO watchlist (user_id, product_id, product_name, created) VALUES(
	1, 
    2, 
    "Hoofdkussen 'Royal'",
    NOW()
);


CREATE TABLE `order`(
	id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    `type` TINYINT NOT NULL DEFAULT 0,
	price_total DECIMAL(10,2) NOT NULL, -- utility, can also be derived from order_entries
    address VARCHAR(255), -- null if type pickup (1)
    pickup_datetime DATETIME, -- null if type delivery (0)
    `status` TINYINT NOT NULL DEFAULT 0, -- 0:pending, 1:completed, 2:cancelled
    PRIMARY KEY (id),
    FOREIGN KEY(user_id) REFERENCES `account`(id)
);

CREATE TABLE `order_entry`(
	id INT AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    shop_id INT NOT NULL, -- stores can delete products but may want to know stats, also easier to look up this way
    quantity INT NOT NULL DEFAULT 1,
    price_per_unit DECIMAL(10,2) NOT NULL, -- could be altered later
    product_name VARCHAR(255), -- could be altered later
    completed BIT(1) DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (order_id) REFERENCES `order`(id)
);

CREATE INDEX productIndx ON order_entry(product_id);
CREATE INDEX orderIndx ON order_entry(order_id);
CREATE INDEX shopIndx ON order_entry(shop_id);


CREATE TABLE message_chain(
	id INT AUTO_INCREMENT,
	user_id INT NOT NULL,
    shop_id INT NOT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated` DATETIME, -- allow null, you can have a conversation without any messages yet
    PRIMARY KEY (id)
);

CREATE INDEX userIndx ON message_chain(user_id);
CREATE INDEX shopIndx ON message_chain(shop_id);

CREATE TABLE messages(
	id INT AUTO_INCREMENT,
    chain_id INT NOT NULL,
    from_user BIT NOT NULL DEFAULT 1,
    user_name VARCHAR(255), -- fallback value if deleted
    shop_name VARCHAR(255), -- fallback value if deleted
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    message TEXT NOT NULL,
    PRIMARY KEY (id),
    foreign key (chain_id) references message_chain(id)
);

CREATE INDEX chainIndx ON messages(chain_id);

CREATE INDEX userIndx ON alert(user_id);

INSERT INTO message_chain VALUES (
	1,
	1,
    1,
    '2022-08-24 06:57:48'
);

INSERT INTO messages VALUES (
	1,
    1,
    1, 
    'Bob Bob',
    'Kussen Winkel',
    '2022-08-24 06:57:48',
    "Hello?\n\nI have some questions about a product of yours that I've just purchased"
);

INSERT INTO messages VALUES (
	2,
    1,
    0, 
    'Bob Bob',
    'Kussen Winkel',
    '2022-08-24 07:57:48',
    "Hello Bob Bob. What is your question?"
);

CREATE TABLE alert (
	id INT auto_increment,
    user_id INT NOT NULL,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `type` TINYINT NOT NULL,
    `seen` BIT(1) NOT NULL DEFAULT 0,
    subject_id INT NOT NULL,
    subject_name VARCHAR(255),
    PRIMARY KEY (id),
    foreign key (user_id) REFERENCES `account`(id)
);

CREATE INDEX usedInds ON alert(user_id);

INSERT INTO alert VALUES (
	1,
    1, 
    '2022-08-24 06:57:48',
	0, -- watchlist available
	0,
    1,
    "Hoofdkussen 'Slaap Lekker'"
);

INSERT INTO alert VALUES (
	2,
    1, 
    '2022-08-24 09:30:48',
	0, -- watchlist available
	0,
    2,
    "Hoofdkussen 'Royal'"
);

INSERT INTO alert VALUES (
	3,
    1, 
    '2022-08-25 12:35:00',
	1, -- product order completed
	0,
    2,
    "Hoofdkussen 'Royal'"
);