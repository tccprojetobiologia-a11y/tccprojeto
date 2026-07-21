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

function renderCalendar($appointments)
{
    $month = date('m');
    $year = date('Y');
    $firstDay = (int) date('w', strtotime($year . '-' . $month . '-01'));
    $daysInMonth = (int) date('t', strtotime($year . '-' . $month . '-01'));
    $html = '<table style="width:100%; border-collapse:collapse; font-size:14px;">';
    $html .= '<thead><tr><th style="padding:8px; background:#f8fafc;">Dom</th><th style="padding:8px; background:#f8fafc;">Seg</th><th style="padding:8px; background:#f8fafc;">Ter</th><th style="padding:8px; background:#f8fafc;">Qua</th><th style="padding:8px; background:#f8fafc;">Qui</th><th style="padding:8px; background:#f8fafc;">Sex</th><th style="padding:8px; background:#f8fafc;">Sáb</th></tr></thead><tbody><tr>';
    for ($i = 0; $i < $firstDay; $i++) {
        $html .= '<td style="padding:8px; color:#cbd5e1; min-height:70px;"></td>';
    }
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = $year . '-' . $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
        $dayAppointments = array_values(array_filter($appointments, function ($item) use ($date) { return ($item['date'] ?? '') === $date; }));
        $html .= '<td style="padding:8px; border:1px solid #f0f0f0; background:#fff; vertical-align:top; min-height:70px;">';
        $html .= '<div style="font-weight:700; color:#1e2a3a;">' . $day . '</div>';
        foreach ($dayAppointments as $appointment) {
            $html .= '<div style="margin-top:6px; font-size:11px; background:#fdf1f4; color:#851e32; padding:4px 6px; border-radius:8px;">' . htmlspecialchars($appointment['time'] ?? '') . ' - ' . htmlspecialchars($appointment['patient_name'] ?? '') . '</div>';
        }
        $html .= '</td>';
        if (($firstDay + $day) % 7 === 0) {
            $html .= '</tr><tr>';
        }
    }
    $html .= '</tr></tbody></table>';
    return $html;
}
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
        .muted { color:#64748b; }
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

        <div class="grid grid-2">
            <div class="card">
                <div style="font-size:14px; color:#64748b;">Consultas pendentes</div>
                <div style="font-size:28px; font-weight:700; color:#851e32; margin-top:6px;"><?php echo $pendingCount; ?></div>
            </div>
            <div class="card">
                <div style="font-size:14px; color:#64748b;">Consultas realizadas</div>
                <div style="font-size:28px; font-weight:700; color:#166534; margin-top:6px;"><?php echo $doneCount; ?></div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Agenda</h3>
            <?php echo renderCalendar($appointments); ?>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Próximos atendimentos</h3>
            <?php if (empty($appointments)) : ?>
                <p class="muted">Nenhuma consulta cadastrada para este médico.</p>
            <?php else : ?>
                <div style="display:grid; gap:12px;">
                    <?php foreach ($appointments as $appointment) : ?>
                        <div style="padding:14px; border:1px solid #f0f0f0; border-radius:12px; display:flex; justify-content:space-between; gap:12px; align-items:center;">
                            <div>
                                <div style="font-weight:700; color:#1e2a3a;"><?php echo htmlspecialchars($appointment['patient_name'] ?? 'Paciente'); ?></div>
                                <div style="font-size:13px; color:#64748b; margin-top:4px;"><?php echo htmlspecialchars($appointment['date'] ?? ''); ?> • <?php echo htmlspecialchars($appointment['time'] ?? ''); ?> • <?php echo htmlspecialchars($appointment['status'] ?? 'Pendente'); ?></div>
                            </div>
                            <a href="medico-dashboard.php?appointment_id=<?php echo urlencode($appointment['id'] ?? ''); ?>" class="btn btn-primary">Atender paciente</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-top:0;">Ficha do paciente</h3>
            <?php if ($selectedAppointment) : ?>
                <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:20px;">
                    <div style="background:#f8fafc; padding:16px; border-radius:12px;">
                        <div style="font-weight:700; color:#1e2a3a; font-size:18px;"><?php echo htmlspecialchars($selectedAppointment['patient_name'] ?? 'Paciente'); ?></div>
                        <div style="font-size:13px; color:#64748b; margin-top:8px;">Idade: <?php echo htmlspecialchars($selectedAppointment['patient_age'] ?? ''); ?></div>
                        <div style="font-size:13px; color:#64748b;">CPF: <?php echo htmlspecialchars($selectedAppointment['cpf'] ?? ''); ?></div>
                        <div style="font-size:13px; color:#64748b;">Telefone: <?php echo htmlspecialchars($selectedAppointment['phone'] ?? ''); ?></div>
                        <div style="margin-top:12px; font-weight:700; color:#1e2a3a;">Sintomas</div>
                        <div style="font-size:13px; color:#475569; margin-top:4px;"><?php echo htmlspecialchars($selectedAppointment['symptoms'] ?? ''); ?></div>
                        <div style="margin-top:12px; font-weight:700; color:#1e2a3a;">Resultados de exames</div>
                        <div style="font-size:13px; color:#475569; margin-top:4px;"><?php echo htmlspecialchars($selectedAppointment['exam_results'] ?? ''); ?></div>
                        <div style="margin-top:12px; font-weight:700; color:#1e2a3a;">Observações do médico</div>
                        <div style="font-size:13px; color:#475569; margin-top:4px;"><?php echo htmlspecialchars($selectedAppointment['doctor_observations'] ?? 'Sem observações.'); ?></div>
                        <div style="margin-top:12px; font-weight:700; color:#1e2a3a;">Exames prescritos</div>
                        <div style="font-size:13px; color:#475569; margin-top:4px;"><?php echo htmlspecialchars($selectedAppointment['prescribed_exams'] ?? 'Nenhum exame prescrito.'); ?></div>
                    </div>
                    <div>
                        <form method="post">
                            <input type="hidden" name="action" value="update_appointment">
                            <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($selectedAppointment['id'] ?? ''); ?>">
                            <label style="display:block; margin-bottom:8px; font-weight:600;">Status</label>
                            <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; margin-bottom:12px;">
                                <option value="Pendente" <?php echo (($selectedAppointment['status'] ?? '') === 'Pendente') ? 'selected' : ''; ?>>Pendente</option>
                                <option value="Realizada" <?php echo (($selectedAppointment['status'] ?? '') === 'Realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="Cancelada" <?php echo (($selectedAppointment['status'] ?? '') === 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                            <label style="display:block; margin-bottom:8px; font-weight:600;">Observações</label>
                            <textarea name="observations" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; margin-bottom:12px;"><?php echo htmlspecialchars($selectedAppointment['doctor_observations'] ?? ''); ?></textarea>
                            <label style="display:block; margin-bottom:8px; font-weight:600;">Prescrever exames</label>
                            <input type="text" name="prescribed_exams" value="<?php echo htmlspecialchars($selectedAppointment['prescribed_exams'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; margin-bottom:12px;">
                            <button class="btn btn-primary" type="submit">Salvar atualização</button>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <p class="muted">Selecione um atendimento para visualizar o histórico do paciente.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
