USE mam;

CREATE TABLE users (
    user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NULL,
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE categories (
    category_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE breeds (
    breed_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE animals (
    animal_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    breed_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    age INT NOT NULL,
    image VARCHAR(255),
    gender ENUM('Male', 'Female', 'Unknown') DEFAULT 'Unknown',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (breed_id) REFERENCES breeds(breed_id)
);

CREATE TABLE password_reset_requests (
    request_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reset_token VARCHAR(100) NOT NULL,
    token_expiry DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE favorite_animals (
    favorite_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_id INT NOT NULL,
    UNIQUE KEY (user_id, animal_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id)
);

CREATE TABLE messages (
    message_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
);

CREATE TABLE adoptions (
    adoption_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_id INT NOT NULL,
    adoption_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id)
);

CREATE TABLE adoption_requests (
    request_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_id INT NOT NULL,
    request_date DATE NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (animal_id) REFERENCES animals(animal_id)
);

-- Add animal types
INSERT INTO categories (name) VALUES
    ('Kutya'),
    ('Macskafélék'),
    ('Madár'),
    ('Rágcsáló'),
    ('Hüllő'),
    ('Akvarisztika'),
    ('Egyéb');

-- Add breeds (dog)
INSERT INTO breeds (name, category_id) VALUES
    ('Német juhász', 1),
    ('Golden Retriever', 1),
    ('Labrador Retriever', 1),
    ('Border Collie', 1),
    ('Rövidszőrű magyar vizsla', 1),
    ('Mopsz', 1),
    ('Si-cu', 1),
    ('Szőrös Tacskó', 1),
    ('Jack Russell terrier', 1),
    ('Bichon frisé', 1),
    ('Törpespicc', 1),
    ('Puli', 1),
    ('Pumi', 1),
    ('Komondor', 1),
    ('Kuvasz', 1),
    ('Cocker spániel', 1);

-- Add breeds (cat)
INSERT INTO breeds (name, category_id) VALUES
    ('Perzsa', 2),
    ('Sziámi', 2),
    ('Bengáli', 2),
    ('Maine Coon', 2),
    ('Brit rövidszőrű', 2),
    ('Keleti rövidszőrű', 2),
    ('Szomáli', 2),
    ('Szfinx', 2),
    ('Szavanna', 2);

-- Add breeds (bird)
INSERT INTO breeds (name, category_id) VALUES
    ('Kanári', 3),
    ('Papagáj', 3),
    ('Budai kanári', 3),
    ('Cinege', 3),
    ('Sármány', 3);

-- Add breeds (rodent)
INSERT INTO breeds (name, category_id) VALUES
    ('Tengerimalac', 4),
    ('Nyúl', 4),
    ('Degu', 4),
    ('Mongol futóegér', 4),
    ('Csincsilla', 4);

-- Add breeds (reptile)
INSERT INTO breeds (name, category_id) VALUES
    ('Görögdenevér', 5),
    ('Gyík', 5),
    ('Teknős', 5),
    ('Kígyó', 5),
    ('Béka', 5);

-- Add breeds (aquaristics)
INSERT INTO breeds (name, category_id) VALUES
    ('Aranyhal', 6),
    ('Szivárványos guppi', 6),
    ('Neonhal', 6),
    ('Angyalhal', 6),
    ('Jukatáni fogasponty', 6);
    

-- Breeds of other categories
INSERT INTO breeds (name, category_id) VALUES
    ('Pók', 7),
    ('Sün', 7),
    ('Rák', 7),
    ('Kaméleon', 7);

