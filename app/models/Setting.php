<?php
/**
 * Model for System Settings
 */

require_once __DIR__ . '/Model.php';

class Setting extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'system_settings';
    }
    
    // Get setting by key
    public function getByKey($key) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE setting_key = ?");
        $stmt->execute([$key]);
        return $stmt->fetch();
    }
    
    // Get setting value by key
    public function getValue($key, $default = null) {
        $setting = $this->getByKey($key);
        return $setting ? $setting['setting_value'] : $default;
    }
    
    // Update or create setting
    public function setSetting($key, $value, $description = '') {
        $existing = $this->getByKey($key);
        
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET setting_value = ?, description = ? WHERE setting_key = ?");
            return $stmt->execute([$value, $description, $key]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (setting_key, setting_value, description) VALUES (?, ?, ?)");
            return $stmt->execute([$key, $value, $description]);
        }
    }
    
    // Get all settings as key-value array
    public function getAllAsArray() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
    
    // Get settings by prefix
    public function getByPrefix($prefix) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE setting_key LIKE ?");
        $stmt->execute([$prefix . '%']);
        return $stmt->fetchAll();
    }
}
