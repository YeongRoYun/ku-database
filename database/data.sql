# Brand
INSERT INTO brands(id, slug, name, image)
VALUES (1, "gs25", "GS25", "https://image.pyoniverse.kr/brands/gs25-logo.webp");
INSERT INTO brands(id, slug, name, image)
VALUES (2, "cu", "CU", "https://image.pyoniverse.kr/brands/cu-logo.webp");
INSERT INTO brands(id, slug, name, image)
VALUES (3, "seven-eleven", "7-Eleven", "https://image.pyoniverse.kr/brands/seveneleven-logo.webp");
INSERT INTO brands(id, slug, name, image)
VALUES (4, "emart24", "Emart24", "https://image.pyoniverse.kr/brands/emart24-logo.webp");
INSERT INTO brands(id, slug, name, image)
VALUES (5, "c-space", "CSpace", "https://image.pyoniverse.kr/brands/cspace-logo.webp");

# categories
INSERT INTO categories(id, slug, name, description)
VALUES (0, "invalid", "Invalid", "분류되지 않은 상품");
INSERT INTO categories(id, slug, name, description)
VALUES (1, "drink", "Drink", "술을 제외한 음료수");
INSERT INTO categories(id, slug, name, description)
VALUES (2, "alcohol", "Alcohol", "술");
INSERT INTO categories(id, slug, name, description)
VALUES (3, "snack", "Snack", "과자류");
INSERT INTO categories(id, slug, name, description)
VALUES (4, "icecream", "Ice Cream", "아이스크림");
INSERT INTO categories(id, slug, name, description)
VALUES (5, "noodle", "Noodle", "라면류");
INSERT INTO categories(id, slug, name, description)
VALUES (6, "lunchbox", "Lunch Box", "도시락류");
INSERT INTO categories(id, slug, name, description)
VALUES (7, "salad", "Salad", "샐러드류");
INSERT INTO categories(id, slug, name, description)
VALUES (8, "kimbab", "Kimbab", "김밥류");
INSERT INTO categories(id, slug, name, description)
VALUES (9, "sandwich", "Sandwich", "샌드위치, 버거류");
INSERT INTO categories(id, slug, name, description)
VALUES (10, "bread", "Bread", "빵류");
INSERT INTO categories(id, slug, name, description)
VALUES (11, "food", "Food", "그 외 식품류");
INSERT INTO categories(id, slug, name, description)
VALUES (12, "household-goods", "Household Goods", "생활용품");

# product_events
INSERT INTO product_events(id, slug, name, description)
VALUES (1, "1+1", "1+1", "동일 상품 하나 더 제공");
INSERT INTO product_events(id, slug, name, description)
VALUES (2, "2+1", "2+1", "동일 상품 두 개 구매시 하나 더 제공");
INSERT INTO product_events(id, slug, name, description)
VALUES (3, "gift", "GIFT", "덤 증정");
INSERT INTO product_events(id, slug, name, description)
VALUES (4, "new", "NEW", "신상품");
INSERT INTO product_events(id, slug, name, description)
VALUES (5, "monopoly", "MONOPOLY", "독점 상품");
INSERT INTO product_events(id, slug, name, description)
VALUES (6, "reservation", "RESERVATION", "예약이 필요한 상품");
INSERT INTO product_events(id, slug, name, description)
VALUES (7, "discount", "DISCOUNT", "할인 상품");
INSERT INTO product_events(id, slug, name, description)
VALUES (8, "3+1", "3+1", "동일 상품 세 개 구매시 하나 더 제공");
