<?php
session_start();
require_once __DIR__ . '/config/auth_storage.php';

if (!isset($_SESSION['logado']) || ($_SESSION['user_role'] ?? '') !== 'doctor') {
    header('Location: index.php');
    exit();
}

$doctor = find_user_by_email($_SESSION['user_email'] ?? '');
if (!$doctor) {
    header('Location: logout.php');
    exit();
}

$doctorId = $doctor['id'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_appointment') {
    $appointmentId = trim($_POST['appointment_id'] ?? '');
    $status = trim($_POST['status'] ?? 'Pendente');
    $observations = trim($_POST['observations'] ?? '');
    $prescribedExams = trim($_POST['prescribed_exams'] ?? '');

    if ($appointmentId !== '') {
        update_appointment_record($appointmentId, [
            'status' => $status,
            'doctor_observations' => $observations,
            'prescribed_exams' => $prescribedExams
        ]);
        $message = '<div style="padding:12px; background:#dcfce7; color:#166534; border-radius:10px; margin-bottom:16px;">Consulta atualizada com sucesso.</div>';
    }
}

$appointments = get_appointments_for_doctor($doctorId);
$selectedId = $_GET['appointment_id'] ?? ($appointments[0]['id'] ?? '');
$selectedAppointment = null;
foreach ($appointments as $appointment) {
    if (($appointment['id'] ?? '') === $selectedId) {
        $selectedAppointment = $appointment;
        break;
    }
}
if (!$selectedAppointment && !empty($appointments)) {
    $selectedAppointment = $appointments[0];
}

$pendingCount = count(array_filter($appointments, function ($item) { return ($item['status'] ?? '') === 'Pendente'; }));
$doneCount = count(array_filter($appointments, function ($item) { return ($item['status'] ?? '') === 'Realizada'; }));
$cancelledCount = count(array_filter($appointments, function ($item) { return ($item['status'] ?? '') === 'Cancelada'; }));
$appointmentsJson = json_encode($appointments, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardioWeb - Portal do Médico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Inter, Arial, sans-serif; margin:0; background:#f6ecee; color:#1f2937; }
        .container { display:flex; min-height:100vh; }
        .sidebar { width:280px; background: linear-gradient(180deg, #4c0719 0%, #7e1b31 100%); color:white; padding:24px; }
        .main { flex:1; padding:24px; overflow:auto; }
        .card { background:white; border-radius:16px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:20px; }
        .btn { padding:10px 14px; border:none; border-radius:10px; cursor:pointer; font-weight:600; }
        .btn-primary { background:#851e32; color:white; }
        .btn-secondary { background:#f3f4f6; color:#374151; }
        .grid { display:grid; gap:16px; }
        .grid-2 { grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); }
        .grid-3 { grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); }
        .muted { color:#64748b; }
        .tab-menu { margin:20px 0 10px; display:flex; flex-direction:column; gap:10px; }
        .tab-button { width:100%; text-align:left; background:transparent; border:1px solid rgba(255,255,255,0.15); color:white; padding:12px 16px; border-radius:12px; cursor:pointer; font-size:15px; display:flex; align-items:center; gap:10px; }
        .tab-button.active { background:rgba(255,255,255,0.15); }
        .tab-button i { width:18px; text-align:center; }
        .tab-view { display:none; }
        .tab-view.active { display:block; }
        .calendar { display:grid; grid-template-columns:repeat(7,1fr); gap:8px; }
        .calendar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .calendar-cell { padding:12px; border:1px solid #e5e7eb; border-radius:12px; min-height:90px; background:white; cursor:pointer; position:relative; display:flex; flex-direction:column; justify-content:space-between; }
        .calendar-cell.disabled { background:#f8fafc; color:#94a3b8; cursor:default; }
        .calendar-cell.selected { border-color:#851e32; box-shadow:0 0 0 3px rgba(133,30,50,0.08); }
        .calendar-dot { width:10px; height:10px; border-radius:50%; margin-top:8px; }
        .calendar-day-name { font-size:12px; color:#64748b; }
        .patient-list-item { padding:14px; border:1px solid #e5e7eb; border-radius:12px; cursor:pointer; transition:background 0.2s; }
        .patient-list-item:hover { background:#f8fafc; }
        .patient-list-item.active { border-color:#851e32; background:#fdf1f4; }
        .hidden { display:none; }
        .status-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
        .status-pendente { background:#fee2e2; color:#b91c1c; }
        .status-realizada { background:#dcfce7; color:#166534; }
        .status-cancelada { background:#fee2e2; color:#b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h2 style="margin-top:0;">CardioWeb</h2>
        <p style="opacity:0.9;">Portal do Médico</p>
        <div style="margin-top:30px; background:rgba(255,255,255,0.12); padding:16px; border-radius:12px;">
            <div style="font-weight:700; font-size:18px;"><?php echo htmlspecialchars($doctor['name'] ?? 'Médico'); ?></div>
            <div style="font-size:13px; opacity:0.85; margin-top:4px;"><?php echo htmlspecialchars($doctor['specialty'] ?? 'Especialidade'); ?></div>
            <div style="font-size:13px; opacity:0.85; margin-top:6px;"><?php echo htmlspecialchars($doctor['email'] ?? ''); ?></div>
        </div>
        <a href="logout.php" style="display:inline-block; margin-top:20px; padding:10px 14px; background:rgba(255,255,255,0.2); color:white; text-decoration:none; border-radius:10px;">Sair</a>
    </aside>
    <main class="main">
        <h1 style="margin-top:0;">Painel do médico</h1>
        <p class="muted">Acompanhe a agenda, os pacientes e atualize o status das consultas.</p>
        <?php echo $message; ?>

        <div class="grid grid-3">
            <div class="card">
                <div style="font-size:14px; color:#64748b;">Consultas pendentes</div>
                <div style="font-size:28px; font-weight:700; color:#851e32; margin-top:6px;"><?php echo $pendingCount; ?></div>
            </div>
            <div class="card">
                <div style="font-size:14px; color:#64748b;">Consultas realizadas</div>
                <div style="font-size:28px; font-weight:700; color:#166534; margin-top:6px;"><?php echo $doneCount; ?></div>
            </div>
            <div class="card">
                <div style="font-size:14px; color:#64748b;">Consultas canceladas</div>
                <div style="font-size:28px; font-weight:700; color:#b91c1c; margin-top:6px;"><?php echo $cancelledCount; ?></div>
            </div>
        </div>

        <div class="card" style="display:flex; gap:20px; flex-wrap:wrap;">
            <div class="tab-menu" style="flex:1; min-width:220px;">
                <button class="tab-button active" data-tab="agenda"><i class="fas fa-calendar-alt"></i>Agenda</button>
                <button class="tab-button" data-tab="pacientes"><i class="fas fa-users"></i>Pacientes</button>
            </div>
            <div style="flex:3; min-width:300px;">
                <div id="agendaTab" class="tab-view active">
                    <div class="card">
                        <div class="calendar-header">
                            <div>
                                <div style="font-size:18px; font-weight:700; color:#1e2a3a;">Calendário</div>
                                <div id="monthLabel" style="color:#64748b; margin-top:4px;"></div>
                            </div>
                        </div>
                        <div class="calendar" id="calendarContainer"></div>
                    </div>

                    <div class="card">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                            <div>
                                <h3 id="dayScheduleTitle" style="margin-top:0;">Agenda do dia</h3>
                                <div id="dayScheduleSubtitle" class="muted">Clique em um dia do calendário para ver os atendimentos.</div>
                            </div>
                            <div id="dayScheduleCount" style="font-weight:700; color:#1e2a3a;"></div>
                        </div>
                        <div id="dayScheduleList" style="margin-top:16px;"></div>
                    </div>
                </div>

                <div id="patientsTab" class="tab-view hidden">
                    <div class="card">
                        <h3 style="margin-top:0;">Pacientes</h3>
                        <div id="patientList" style="display:grid; gap:12px;"></div>
                    </div>
                    <div class="card">
                        <h3 style="margin-top:0;">Histórico médico</h3>
                        <div id="patientHistory" style="margin-top:16px;"></div>
                        <div id="patientDetailCard" style="margin-top:16px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const appointments = <?php echo $appointmentsJson; ?>;
    const selectedData = { appointmentId: '<?php echo htmlspecialchars($selectedAppointment['id'] ?? '', ENT_QUOTES); ?>' };
    let activeTab = 'agenda';
    let selectedDate = appointments.length > 0 ? appointments[0].date : new Date().toISOString().slice(0, 10);
    let selectedPatient = null;

    const monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const dayNames = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];

    function initDoctorPanel() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => switchTab(button.dataset.tab));
        });
        renderCalendar();
        renderPatients();
        selectDate(selectedDate);
    }

    function switchTab(tab) {
        activeTab = tab;
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.toggle('active', button.dataset.tab === tab);
        });
        document.querySelectorAll('.tab-view').forEach(view => {
            view.classList.toggle('active', view.id === tab + 'Tab');
            view.classList.toggle('hidden', view.id !== tab + 'Tab');
        });
    }

    function renderCalendar() {
        const container = document.getElementById('calendarContainer');
        container.innerHTML = '';

        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        document.getElementById('monthLabel').innerText = `${monthNames[month]} ${year}`;

        dayNames.forEach(day => {
            const header = document.createElement('div');
            header.className = 'calendar-cell disabled';
            header.style.textAlign = 'center';
            header.style.fontWeight = '700';
            header.innerText = day;
            container.appendChild(header);
        });

        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'calendar-cell disabled';
            container.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayAppointments = appointments.filter(a => a.date === dateKey);
            const pending = dayAppointments.some(a => a.status === 'Pendente');
            const allDone = dayAppointments.length > 0 && dayAppointments.every(a => a.status === 'Realizada');
            const dotColor = dayAppointments.length === 0 ? 'transparent' : allDone ? '#22c55e' : '#ef4444';

            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            if (selectedDate === dateKey) cell.classList.add('selected');
            cell.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <strong>${day}</strong>
                    <span style="font-size:11px; color:#64748b;">${dayNames[new Date(dateKey).getDay()]}</span>
                </div>
                <div style="flex:1;"></div>
                <div style="display:flex; justify-content:center;"><span class="calendar-dot" style="background:${dotColor};"></span></div>
            `;
            cell.addEventListener('click', () => selectDate(dateKey));
            container.appendChild(cell);
        }
    }

    function selectDate(dateKey) {
        selectedDate = dateKey;
        document.querySelectorAll('.calendar-cell').forEach(cell => {
            cell.classList.remove('selected');
        });
        const cells = document.querySelectorAll('#calendarContainer .calendar-cell');
        cells.forEach(cell => {
            if (cell.textContent.trim().startsWith(String(Number(dateKey.split('-')[2])))) {
                const headerText = cell.querySelector('strong');
                if (headerText && headerText.innerText === String(Number(dateKey.split('-')[2]))) {
                    cell.classList.add('selected');
                }
            }
        });
        renderDaySchedule(dateKey);
    }

    function renderDaySchedule(dateKey) {
        const appointmentsByDate = appointments.filter(a => a.date === dateKey).sort((a,b) => a.time.localeCompare(b.time));
        const title = new Date(dateKey).toLocaleDateString('pt-BR', { day:'2-digit', month:'2-digit', year:'numeric' });
        document.getElementById('dayScheduleTitle').innerText = `Agenda de ${title}`;
        const dayStatus = appointmentsByDate.length === 0 ? 'Nenhuma consulta agendada.' : `${appointmentsByDate.length} consulta(s) neste dia.`;
        document.getElementById('dayScheduleSubtitle').innerText = dayStatus;
        document.getElementById('dayScheduleCount').innerText = appointmentsByDate.length ? `${appointmentsByDate.length} pacientes` : '';

        const list = document.getElementById('dayScheduleList');
        list.innerHTML = '';
        if (appointmentsByDate.length === 0) {
            list.innerHTML = '<p class="muted">Nenhuma consulta marcada para este dia.</p>';
            return;
        }

        appointmentsByDate.forEach(appointment => {
            const card = document.createElement('div');
            card.style.padding = '14px';
            card.style.border = '1px solid #e5e7eb';
            card.style.borderRadius = '12px';
            card.style.marginBottom = '12px';
            card.style.background = '#ffffff';
            card.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div class="patient-select" style="font-weight:700; color:#1e2a3a; cursor:pointer;">${appointment.patient_name}</div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;">${appointment.time} • ${appointment.patient_age} anos</div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;">CPF: ${appointment.cpf} • ${appointment.phone}</div>
                    </div>
                    <div class="status-pill ${appointment.status === 'Realizada' ? 'status-realizada' : appointment.status === 'Cancelada' ? 'status-cancelada' : 'status-pendente'}">${appointment.status}</div>
                </div>
                <div style="margin-top:12px; color:#475569; font-size:13px;">${appointment.symptoms}</div>
                <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="btn btn-secondary appointment-detail-button" type="button" data-appointment-id="${appointment.id}">Ver ficha</button>
                    <button class="btn btn-primary appointment-detail-button" type="button" data-appointment-id="${appointment.id}">Atualizar status</button>
                </div>
            `;
            card.querySelector('.patient-select').addEventListener('click', () => selectPatient(appointment.patient_name, appointment.id));
            card.querySelectorAll('.appointment-detail-button').forEach(button => {
                button.addEventListener('click', () => showAppointmentDetails(button.dataset.appointmentId));
            });
            list.appendChild(card);
        });
    }

    function renderPatients() {
        const patientList = document.getElementById('patientList');
        patientList.innerHTML = '';
        const uniquePatients = {};
        appointments.forEach(appointment => {
            const name = appointment.patient_name;
            const key = `${name}|${appointment.cpf}`;
            if (!uniquePatients[key]) {
                uniquePatients[key] = { name, cpf: appointment.cpf, phone: appointment.phone, age: appointment.patient_age, appointments: [] };
            }
            uniquePatients[key].appointments.push(appointment);
        });

        Object.values(uniquePatients).sort((a, b) => a.name.localeCompare(b.name)).forEach(patient => {
            const item = document.createElement('div');
            item.className = 'patient-list-item';
            item.dataset.patientName = patient.name;
            item.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700; color:#1e2a3a;">${patient.name}</div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;">${patient.appointments.length} consulta(s)</div>
                    </div>
                    <div style="font-size:12px; color:#64748b;">${patient.cpf}</div>
                </div>
            `;
            item.addEventListener('click', () => selectPatient(patient.name));
            patientList.appendChild(item);
        });

        if (Object.keys(uniquePatients).length > 0 && !selectedPatient) {
            selectedPatient = Object.values(uniquePatients)[0].name;
        }
        renderPatientHistory();
    }

    function selectPatient(name, appointmentId = '') {
        selectedPatient = name;
        document.querySelectorAll('.patient-list-item').forEach(item => {
            item.classList.toggle('active', item.dataset.patientName === name);
        });
        renderPatientHistory(appointmentId);
        switchTab('pacientes');
    }

    function renderPatientHistory(appointmentId = '') {
        const historyContainer = document.getElementById('patientHistory');
        if (!selectedPatient) {
            historyContainer.innerHTML = '<p class="muted">Selecione um paciente para ver o histórico.</p>';
            return;
        }
        const patientAppointments = appointments.filter(a => a.patient_name === selectedPatient).sort((a,b) => b.date.localeCompare(a.date) || b.time.localeCompare(a.time));
        if (patientAppointments.length === 0) {
            historyContainer.innerHTML = '<p class="muted">Nenhum histórico encontrado para este paciente.</p>';
            return;
        }

        const patient = patientAppointments[0];
        const appointmentRows = patientAppointments.map(app => `
            <div style="padding:14px; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:12px; background:#ffffff;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700; color:#1e2a3a;">${app.date} • ${app.time}</div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;">Status: ${app.status}</div>
                    </div>
                    <button class="btn btn-primary" type="button" onclick="showAppointmentDetails('${app.id}')">Ver detalhes</button>
                </div>
            </div>
        `).join('');

        historyContainer.innerHTML = `
            <div style="display:grid; gap:16px;">
                <div style="padding:20px; border:1px solid #e5e7eb; border-radius:16px; background:#f8fafc;">
                    <div style="font-weight:700; color:#1e2a3a;">${patient.name}</div>
                    <div style="margin-top:8px; color:#64748b;">Idade: ${patient.age} anos • CPF: ${patient.cpf} • Tel: ${patient.phone}</div>
                    <div style="margin-top:12px; color:#475569;">Consultas totais: ${patientAppointments.length}</div>
                </div>
                <div>
                    <h4 style="margin-top:0;">Consultas atuais e passadas</h4>
                    ${appointmentRows}
                </div>
            </div>
        `;

        if (appointmentId) {
            showAppointmentDetails(appointmentId);
        }
    }

    function showAppointmentDetails(appointmentId) {
        const appointment = appointments.find(a => a.id === appointmentId);
        if (!appointment) return;
        const target = activeTab === 'pacientes' ? document.getElementById('patientDetailCard') : document.getElementById('dayScheduleList');
        if (!target) return;
        target.innerHTML = `
            <div style="padding:18px; border:1px solid #e5e7eb; border-radius:16px; background:#ffffff; margin-top:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700; color:#1e2a3a; font-size:18px;">${appointment.patient_name}</div>
                        <div style="margin-top:6px; color:#64748b;">${appointment.date} • ${appointment.time}</div>
                    </div>
                    <div class="status-pill ${appointment.status === 'Realizada' ? 'status-realizada' : appointment.status === 'Cancelada' ? 'status-cancelada' : 'status-pendente'}">${appointment.status}</div>
                </div>
                <div style="margin-top:16px; display:grid; gap:10px;">
                    <div><strong>Sintomas:</strong> ${appointment.symptoms}</div>
                    <div><strong>Exames:</strong> ${appointment.exam_results}</div>
                    <div><strong>Observações:</strong> ${appointment.doctor_observations || 'Nenhuma observação.'}</div>
                    <div><strong>Prescrição:</strong> ${appointment.prescribed_exams || 'Nenhum exame prescrito.'}</div>
                </div>
                <form method="post" style="margin-top:18px; display:grid; gap:12px;">
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="${appointment.id}">
                    <label style="font-weight:600;">Status</label>
                    <select name="status" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
                        <option value="Pendente" ${appointment.status === 'Pendente' ? 'selected' : ''}>Pendente</option>
                        <option value="Realizada" ${appointment.status === 'Realizada' ? 'selected' : ''}>Realizada</option>
                        <option value="Cancelada" ${appointment.status === 'Cancelada' ? 'selected' : ''}>Cancelada</option>
                    </select>
                    <label style="font-weight:600;">Observações</label>
                    <textarea name="observations" rows="4" style="padding:10px; border:1px solid #ddd; border-radius:10px;">${appointment.doctor_observations || ''}</textarea>
                    <label style="font-weight:600;">Prescrever exames</label>
                    <input type="text" name="prescribed_exams" value="${appointment.prescribed_exams || ''}" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
                    <button class="btn btn-primary" type="submit">Salvar atualização</button>
                </form>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', initDoctorPanel);
</script>
</body>
</html>
