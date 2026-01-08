<!-- Vista del Calendario -->

<div class="bg-white rounded-lg shadow-lg p-6">
    <div id="calendar"></div>
</div>

<!-- Modal para crear/editar eventos -->
<div id="eventModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" id="modalTitle">Nuevo Evento</h3>
            
            <form id="eventForm">
                <input type="hidden" id="eventId" name="eventId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                    <input type="text" id="eventTitle" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="eventDescription" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select id="eventType" name="event_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="maintenance">Mantenimiento</option>
                        <option value="inspection">Inspección</option>
                        <option value="meeting">Reunión</option>
                        <option value="training">Capacitación</option>
                        <option value="other">Otro</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha/Hora Inicio *</label>
                    <input type="datetime-local" id="eventStart" name="start_date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha/Hora Fin *</label>
                    <input type="datetime-local" id="eventEnd" name="end_date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                    <input type="text" id="eventLocation" name="location"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="eventAllDay" name="all_day" class="mr-2">
                        <span class="text-sm text-gray-700">Todo el día</span>
                    </label>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEventModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        events: '<?php echo BASE_URL; ?>/calendar/getEvents',
        editable: true,
        selectable: true,
        
        // Al hacer clic en una fecha vacía
        select: function(info) {
            document.getElementById('modalTitle').textContent = 'Nuevo Evento';
            document.getElementById('eventForm').reset();
            document.getElementById('eventId').value = '';
            document.getElementById('eventStart').value = info.startStr.slice(0, 16);
            document.getElementById('eventEnd').value = info.endStr.slice(0, 16);
            document.getElementById('eventModal').classList.remove('hidden');
        },
        
        // Al hacer clic en un evento existente
        eventClick: function(info) {
            if (confirm('¿Desea eliminar este evento?')) {
                if (deleteEvent(info.event.id)) {
                    info.event.remove();
                }
            }
        },
        
        // Al mover un evento
        eventDrop: function(info) {
            updateEventDates(info.event);
        },
        
        // Al redimensionar un evento
        eventResize: function(info) {
            updateEventDates(info.event);
        }
    });
    
    calendar.render();
    
    // Manejar envío del formulario
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const eventData = {
            title: formData.get('title'),
            description: formData.get('description'),
            event_type: formData.get('event_type'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
            location: formData.get('location'),
            all_day: formData.get('all_day') ? 1 : 0,
            color: getColorForType(formData.get('event_type'))
        };
        
        const eventId = formData.get('eventId');
        
        if (eventId) {
            // Actualizar evento existente
            updateEvent(eventId, eventData).then(() => {
                calendar.refetchEvents();
                closeEventModal();
            });
        } else {
            // Crear nuevo evento
            createEvent(eventData).then(() => {
                calendar.refetchEvents();
                closeEventModal();
            });
        }
    });
});

function closeEventModal() {
    document.getElementById('eventModal').classList.add('hidden');
}

function getColorForType(type) {
    const colors = {
        'maintenance': '#e74c3c',
        'inspection': '#f39c12',
        'meeting': '#9b59b6',
        'training': '#3498db',
        'other': '#95a5a6'
    };
    return colors[type] || '#3788d8';
}

async function createEvent(eventData) {
    const response = await fetch('<?php echo BASE_URL; ?>/calendar/createEvent', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(eventData)
    });
    return response.json();
}

async function updateEvent(eventId, eventData) {
    const response = await fetch('<?php echo BASE_URL; ?>/calendar/updateEvent/' + eventId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(eventData)
    });
    return response.json();
}

async function deleteEvent(eventId) {
    const response = await fetch('<?php echo BASE_URL; ?>/calendar/deleteEvent/' + eventId, {
        method: 'POST'
    });
    const result = await response.json();
    return result.success;
}

async function updateEventDates(event) {
    const eventData = {
        title: event.title,
        start_date: event.start.toISOString().slice(0, 19).replace('T', ' '),
        end_date: event.end ? event.end.toISOString().slice(0, 19).replace('T', ' ') : event.start.toISOString().slice(0, 19).replace('T', ' '),
        event_type: event.extendedProps.type,
        description: event.extendedProps.description,
        location: event.extendedProps.location,
        all_day: event.allDay ? 1 : 0,
        color: event.backgroundColor
    };
    
    await updateEvent(event.id, eventData);
}
</script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}

.fc-event {
    cursor: pointer;
}
</style>
