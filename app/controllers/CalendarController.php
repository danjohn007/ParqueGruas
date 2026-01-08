<?php
/**
 * Controlador de Calendario
 */

require_once __DIR__ . '/Controller.php';

class CalendarController extends Controller {
    
    // Vista del calendario
    public function index() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Calendario de Actividades',
            'subtitle' => 'Mantenimientos, inspecciones y eventos programados'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/calendar/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // API: Obtener eventos
    public function getEvents() {
        $this->requireAuth();
        
        $db = Database::getInstance()->getConnection();
        
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        
        $sql = "SELECT * FROM calendar_events WHERE 1=1";
        $params = [];
        
        if ($start) {
            $sql .= " AND start_date >= ?";
            $params[] = $start;
        }
        
        if ($end) {
            $sql .= " AND end_date <= ?";
            $params[] = $end;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
        
        // Formatear para FullCalendar
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start_date'],
                'end' => $event['end_date'],
                'allDay' => (bool)$event['all_day'],
                'color' => $event['color'],
                'extendedProps' => [
                    'description' => $event['description'],
                    'type' => $event['event_type'],
                    'location' => $event['location']
                ]
            ];
        }
        
        $this->json($formattedEvents);
    }
    
    // API: Crear evento
    public function createEvent() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO calendar_events 
            (title, description, event_type, start_date, end_date, all_day, location, created_by, color) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['event_type'] ?? 'other',
            $data['start_date'] ?? '',
            $data['end_date'] ?? '',
            $data['all_day'] ?? false,
            $data['location'] ?? '',
            $_SESSION['user_id'],
            $data['color'] ?? '#3788d8'
        ]);
        
        if ($result) {
            $this->json(['success' => true, 'id' => $db->lastInsertId()]);
        } else {
            $this->json(['error' => 'Error al crear evento'], 500);
        }
    }
    
    // API: Actualizar evento
    public function updateEvent($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            UPDATE calendar_events 
            SET title = ?, description = ?, event_type = ?, start_date = ?, end_date = ?, 
                all_day = ?, location = ?, color = ?
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['event_type'] ?? 'other',
            $data['start_date'] ?? '',
            $data['end_date'] ?? '',
            $data['all_day'] ?? false,
            $data['location'] ?? '',
            $data['color'] ?? '#3788d8',
            $id
        ]);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Error al actualizar evento'], 500);
        }
    }
    
    // API: Eliminar evento
    public function deleteEvent($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM calendar_events WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Error al eliminar evento'], 500);
        }
    }
}
