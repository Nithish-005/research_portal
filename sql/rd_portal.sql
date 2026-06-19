CREATE DATABASE rd_portal CHARACTER SET utf8mb4;
USE rd_portal;

CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name     VARCHAR(100),
  role          ENUM('staff','research_admin','hod') DEFAULT 'staff',
  target_j      TINYINT DEFAULT 4,   -- journals
  target_p      TINYINT DEFAULT 3,   -- papers
  cycle_id      TINYINT DEFAULT 1,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cycles (
  id          TINYINT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(20),
  start_date  DATE,
  end_date    DATE,
  is_active   TINYINT(1) DEFAULT 0
);

INSERT INTO cycles(name,start_date,end_date,is_active) VALUES
('Cycle 1','2024-07-01','2025-06-30',1);

CREATE TABLE journals (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  cycle_id    TINYINT NOT NULL,
  title       VARCHAR(255),
  authors     VARCHAR(255),
  journal_name VARCHAR(255),
  issn        VARCHAR(50),
  doi         VARCHAR(100),
  status      ENUM('Submitted','Accepted','Published') DEFAULT 'Submitted',
  vol         VARCHAR(20),
  issue       VARCHAR(20),
  pages       VARCHAR(20),
  impact_factor VARCHAR(20),
  indexing    VARCHAR(100),
  pdf_path    VARCHAR(255),   -- path to uploaded file
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)  REFERENCES users(id),
  FOREIGN KEY (cycle_id) REFERENCES cycles(id)
);

-- seed one HOD, one staff (password = 123456)
INSERT INTO users(username,password_hash,full_name,role,target_j,target_p)
VALUES
('hod@velammal.edu',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Dr HOD','hod',4,3),
('staff@velammal.edu',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Dr A Staff','staff',4,3);