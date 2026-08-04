<?php
require_once __DIR__ . '/config/auth_storage.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_doctor') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Preencha nome, e-mail e senha para criar o médico.</div>';
    } else {
        $created = create_doctor_profile($name, $email, $password, $specialty, $phone);
        if ($created) {
            $message = '<div style="padding:12px; background:#dcfce7; color:#166534; border-radius:10px; margin-bottom:16px;">Médico criado com sucesso. O acesso inicial foi salvo e o perfil já pode receber agenda.</div>';
        } else {
            $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Já existe um médico com este e-mail.</div>';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_appointment') {
    $doctorId = trim($_POST['doctor_id'] ?? '');
    $patientName = trim($_POST['patient_name'] ?? '');
    $patientAge = (int) ($_POST['patient_age'] ?? 0);
    $cpf = trim($_POST['cpf'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '09:00');
    $status = trim($_POST['status'] ?? 'Pendente');
    $symptoms = trim($_POST['symptoms'] ?? 'Consulta agendada pelo admin.');

    if ($doctorId !== '' && $patientName !== '' && $date !== '') {
        create_appointment_record($doctorId, $patientName, $patientAge, $cpf, $phone, $symptoms, '', '', '', $status, $date, $time);
        $message = '<div style="padding:12px; background:#dcfce7; color:#166534; border-radius:10px; margin-bottom:16px;">Consulta agendada com sucesso para o médico selecionado.</div>';
    } else {
        $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Preencha médico, paciente e data para agendar.</div>';
    }
}

$doctors = get_doctors();
$appointments = get_all_appointments();
$doctorById = [];
foreach ($doctors as $doctor) {
    $doctorById[$doctor['id']] = $doctor['name'];
}
?>
<div style="margin-bottom: 28px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Gerenciar Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Cadastre novos médicos, agende consultas e acompanhe o calendário do admin em tempo real.</p>
</div>

<?php echo $message; ?>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; align-items:start;">
    <div style="background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="margin-bottom:16px; color:#1e2a3a;">Criar novo perfil de médico</h4>
        <form method="post">
            <input type="hidden" name="action" value="create_doctor">
            <div style="display:grid; gap:12px;">
                <input type="text" name="name" placeholder="Nome do médico" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
                <input type="email" name="email" placeholder="E-mail do médico" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
                <input type="password" name="password" placeholder="Senha inicial" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
                <input type="text" name="specialty" placeholder="Especialidade" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
                <input type="text" name="phone" placeholder="Telefone" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
                <button type="submit" style="padding:12px; background:#851e32; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer;">Criar perfil</button>
            </div>
        </form>
    </div>

    <div style="background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h4 style="margin-bottom:16px; color:#1e2a3a;">Médicos cadastrados</h4>
        <?php if (empty($doctors)) : ?>
            <p style="color:#64748b;">Nenhum médico cadastrado ainda.</p>
        <?php else : ?>
            <div style="display:grid; gap:12px;">
                <?php foreach ($doctors as $doctor) : ?>
                    <div style="padding:14px; border:1px solid #f0f0f0; border-radius:12px;">
                        <div style="font-weight:700; color:#1e2a3a;"><?php echo htmlspecialchars($doctor['name'] ?? 'Médico'); ?></div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;"><?php echo htmlspecialchars($doctor['specialty'] ?? 'Especialidade não informada'); ?></div>
                        <div style="font-size:13px; color:#64748b; margin-top:4px;">E-mail: <?php echo htmlspecialchars($doctor['email'] ?? ''); ?></div>
                        <div style="font-size:13px; color:#64748b;">Senha inicial: <?php echo htmlspecialchars($doctor['password'] ?? ''); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div style="background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-top:24px;">
    <h4 style="margin-bottom:16px; color:#1e2a3a;">Agendar consulta pelo admin</h4>
    <form method="post" style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:12px;">
        <input type="hidden" name="action" value="create_appointment">
        <select name="doctor_id" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
            <option value="">Selecione o médico</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo htmlspecialchars($doctor['id']); ?>"><?php echo htmlspecialchars($doctor['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="patient_name" placeholder="Nome do paciente" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="number" name="patient_age" min="0" placeholder="Idade" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="cpf" placeholder="CPF" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="phone" placeholder="Telefone" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="date" name="date" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="time" name="time" value="09:00" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <select name="status" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
            <option value="Pendente">Pendente</option>
            <option value="Confirmada">Confirmada</option>
            <option value="Cancelada">Cancelada</option>
        </select>
        <textarea name="symptoms" rows="3" placeholder="Sintomas / observações" style="grid-column: 1 / -1; padding:12px; border:1px solid #ddd; border-radius:10px; resize:vertical;"></textarea>
        <button type="submit" style="grid-column:1 / -1; padding:12px; background:#851e32; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer;">Agendar consulta</button>
    </form>
</div>

<div style="background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-top:24px;">
    <h4 style="margin-bottom:16px; color:#1e2a3a;">Consultas agendadas</h4>
    <?php if (empty($appointments)) : ?>
        <p style="color:#64748b;">Nenhuma consulta agendada até o momento.</p>
    <?php else : ?>
        <div style="display:grid; gap:12px;">
            <?php foreach ($appointments as $appointment) : ?>
                <div style="border:1px solid #f0f0f0; border-radius:12px; padding:14px;">
                    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:8px; align-items:center;">
                        <div>
                            <strong style="color:#1e2a3a; font-size:16px;"><?php echo htmlspecialchars($appointment['patient_name'] ?? 'Paciente'); ?></strong>
                            <div style="font-size:13px; color:#64748b;">Médico: <?php echo htmlspecialchars($doctorById[$appointment['doctor_id']] ?? 'Não informado'); ?></div>
                        </div>
                        <span style="padding:4px 10px; border-radius:999px; background:#f1f5f9; color:#0f172a; font-size:12px; font-weight:700;">
                            <?php echo htmlspecialchars($appointment['status'] ?? 'Pendente'); ?>
                        </span>
                    </div>
                    <div style="margin-top:10px; font-size:13px; color:#64748b;">
                        <?php echo htmlspecialchars($appointment['date'] ?? ''); ?> • <?php echo htmlspecialchars($appointment['time'] ?? '09:00'); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
