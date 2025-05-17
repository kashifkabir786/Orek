-- Orek Analytics System Database Tables
-- Execute this SQL to set up the required tables for tracking

-- Table for basic visit data
CREATE TABLE IF NOT EXISTS site_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    page_url VARCHAR(255) NOT NULL,
    referrer VARCHAR(255),
    visit_time DATETIME NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    visitor_id VARCHAR(255) NOT NULL,
    device_type VARCHAR(20) NOT NULL,
    browser VARCHAR(50) NOT NULL,
    os VARCHAR(50) NOT NULL,
    is_logged_in TINYINT(1) NOT NULL DEFAULT 0,
    user_id INT,
    traffic_source VARCHAR(50) NOT NULL DEFAULT 'Direct',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_session_id (session_id),
    INDEX idx_visit_time (visit_time),
    INDEX idx_user_id (user_id),
    INDEX idx_page_url (page_url),
    INDEX idx_traffic_source (traffic_source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for performance metrics
CREATE TABLE IF NOT EXISTS performance_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(255) NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    load_time INT NOT NULL DEFAULT 0,
    render_time INT NOT NULL DEFAULT 0,
    total_time INT NOT NULL DEFAULT 0,
    screen_width INT NOT NULL DEFAULT 0,
    screen_height INT NOT NULL DEFAULT 0,
    color_depth INT NOT NULL DEFAULT 0,
    viewport_width INT NOT NULL DEFAULT 0,
    viewport_height INT NOT NULL DEFAULT 0,
    timestamp DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_page_url (page_url),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for user interaction events
CREATE TABLE IF NOT EXISTS user_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id VARCHAR(255) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    timestamp DATETIME NOT NULL,
    element_type VARCHAR(50),
    element_id VARCHAR(100),
    element_class VARCHAR(255),
    element_text TEXT,
    link_url VARCHAR(255),
    scroll_depth INT,
    time_spent INT,
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visitor_id (visitor_id),
    INDEX idx_event_type (event_type),
    INDEX idx_page_url (page_url),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for aggregated daily statistics
CREATE TABLE IF NOT EXISTS analytics_daily_summary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    page_views INT NOT NULL DEFAULT 0,
    unique_visitors INT NOT NULL DEFAULT 0,
    avg_time_on_page INT NOT NULL DEFAULT 0,
    bounce_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    desktop_views INT NOT NULL DEFAULT 0,
    mobile_views INT NOT NULL DEFAULT 0,
    tablet_views INT NOT NULL DEFAULT 0,
    direct_traffic INT NOT NULL DEFAULT 0,
    search_traffic INT NOT NULL DEFAULT 0,
    referral_traffic INT NOT NULL DEFAULT 0,
    social_traffic INT NOT NULL DEFAULT 0,
    avg_load_time INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_date_page (date, page_url),
    INDEX idx_date (date),
    INDEX idx_page_url (page_url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 