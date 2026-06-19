# 📚 Research & Development Portal

## Academic Research Tracking & Publication Management System

The R&D Portal is a web-based research management platform designed for educational institutions and research organizations to track faculty publications, manage research cycles, monitor progress against publication targets, and generate department-wide reports.

The platform provides role-based dashboards for Staff, Heads of Department (HOD), and Research Administrators, enabling efficient monitoring of journals, research papers, publication performance, and academic productivity.

---

## 🚀 Key Features

### 👨‍🏫 Faculty Research Management

* Journal publication submission
* Research paper tracking
* PDF document uploads
* Publication status management
* Personal progress monitoring

### 🏢 Department Management

* Staff publication tracking
* Department performance overview
* Research productivity monitoring
* Cycle-based target evaluation
* Progress analytics dashboard

### 👨‍💼 Research Administration

* Institution-wide publication management
* Journal status updates
* Publication approval workflows
* Record modification and deletion
* Report generation

### 📊 Progress Tracking

* Journal publication targets
* Research paper targets
* Completion percentage indicators
* Staff-wise performance monitoring
* Department achievement analysis

### 📑 Report Generation

* Excel report export
* Cycle-wise publication reports
* Research activity summaries
* Department analytics

---

## 🏗️ System Architecture

```text
┌─────────────────────┐
│     Web Browser     │
│ HTML • CSS • JS     │
└──────────┬──────────┘
           │ HTTP
           ▼
┌─────────────────────┐
│     PHP Backend     │
│ Business Logic      │
└──────────┬──────────┘
           │ PDO
           ▼
┌─────────────────────┐
│   MySQL Database    │
│ Publication Records │
└─────────────────────┘
```

---

## 👥 User Roles

### 👨‍🏫 Staff

Staff members can:

* Submit journal publications
* Upload supporting PDF documents
* Track publication progress
* View publication history
* Monitor target achievement

### 👨‍💼 Head of Department (HOD)

HODs can:

* View department statistics
* Monitor staff performance
* Track publication targets
* Close academic research cycles
* Generate publication reports

### 🏢 Research Administrator

Research Administrators can:

* Manage all publication records
* Edit publication status
* Delete entries
* Generate institutional reports
* Monitor overall research activity

---

## 🔄 Research Workflow

### Publication Submission Process

1. Staff login to portal
2. Submit journal publication details
3. Upload supporting PDF document
4. Record stored in database
5. Publication status tracked
6. Progress updated automatically
7. Available for department review

### Cycle Management Process

1. Active research cycle monitored
2. HOD reviews department performance
3. Cycle closure initiated
4. New cycle automatically created
5. User cycle assignments updated
6. Reporting generated for completed cycle

---

## 📊 Dashboard Features

### Staff Dashboard

* Personal journal count
* Research paper count
* Publication targets
* Progress indicators
* Journal submission form
* Publication history table

### HOD Dashboard

* Total staff count
* Department journal statistics
* Research paper statistics
* Staff performance overview
* Progress tracking charts
* Cycle management controls

### Research Admin Dashboard

* Complete publication repository
* Edit publication records
* Delete publication entries
* Institution-wide analytics
* Report generation tools

---

## 🛠️ Technology Stack

### Frontend

* HTML5
* CSS3
* JavaScript (Vanilla JS)

### Backend

* PHP 8+
* PDO Database Layer

### Database

* MySQL

### Reporting

* PhpSpreadsheet
* Excel Export (.xlsx)

### Security

* Session Authentication
* Password Hashing (bcrypt)
* CSRF Protection
* Prepared SQL Statements
* File Validation

---

## 📂 Project Structure

```text
rd-portal/
│
├── auth.php
├── logout.php
├── db.php
│
├── staff_dashboard.php
├── hod_dashboard.php
├── research_admin_dashboard.php
│
├── add_journal.php
├── update_entry.php
├── delete_entry.php
│
├── close_cycle.php
├── generate_report.php
│
├── uploads/
│
├── sql/
│   └── rd_portal.sql
│
└── assets/
```

---

## 🚀 Installation

### Prerequisites

* PHP 8+
* MySQL 8+
* Apache / XAMPP / WAMP / Laragon
* Composer (for report generation)

---

## Database Setup

Create a database:

```sql
CREATE DATABASE rd_portal;
```

Import schema:

```bash
mysql -u root -p rd_portal < sql/rd_portal.sql
```

---

## Configure Database

Update database credentials inside:

```php
db.php
```

Example:

```php
$host = "localhost";
$dbname = "rd_portal";
$user = "root";
$password = "";
```

---

## Install Report Dependencies

```bash
composer require phpoffice/phpspreadsheet
```

---

## Run Application

Place project inside:

```text
htdocs/      (XAMPP)
www/         (WAMP)
public_html/ (Hosting)
```

Access:

```text
http://localhost/rd-portal
```

---

## 🔐 Security Features

* Password hashing using bcrypt
* Session fixation protection
* CSRF token validation
* SQL injection prevention
* XSS protection using htmlspecialchars()
* PDF MIME-type verification
* Role-based authorization

---

## 📈 Database Modules

### Users

Stores:

* Faculty information
* Login credentials
* User roles
* Publication targets

### Cycles

Stores:

* Academic cycle details
* Start and end dates
* Active cycle information

### Journals

Stores:

* Publication metadata
* DOI details
* ISSN information
* Indexing information
* Publication status
* Uploaded PDFs

### Papers

Stores:

* Research paper records
* Publication information
* Cycle associations

---

## 📑 Report Generation

The system can generate:

* Journal publication reports
* Cycle-wise reports
* Department summaries
* Staff performance reports
* Excel export files

---

## 🎯 Use Cases

### Educational Institutions

* Faculty performance tracking
* NAAC/NBA documentation
* Accreditation support
* Research monitoring

### Research Organizations

* Publication management
* Research output analysis
* Progress monitoring
* Reporting and compliance

---

## 🔮 Future Enhancements

* Research paper submission module
* Email notifications
* Publication approval workflow
* Advanced analytics dashboard
* REST API integration
* Multi-department management
* Research KPI visualization
* Cloud document storage

---

## 📜 License

MIT License

---

## 👨‍💻 Project Objective

The R&D Portal aims to digitize research publication management by providing a centralized platform for tracking journals, monitoring faculty research performance, managing academic cycles, and generating institutional research reports efficiently.

**Empowering Research Excellence Through Digital Transformation**
