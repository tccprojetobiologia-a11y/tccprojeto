<?php
require_once __DIR__ . '/config/auth_storage.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_doctor') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $birthDate = trim($_POST['birth_date'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $height = trim($_POST['height'] ?? '');
        $weight = trim($_POST['weight'] ?? '');
        $cep = trim($_POST['cep'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $neighborhood = trim($_POST['neighborhood'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $crm = trim($_POST['crm'] ?? '');
        $emergencyContact = trim($_POST['emergency_contact'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Preencha nome, e-mail e senha para criar o médico.</div>';
        } else {
            $created = create_doctor_profile($name, $email, $password, $specialty, $phone, [
                'cpf' => $cpf,
                'birth_date' => $birthDate,
                'age' => $age,
                'height' => $height,
                'weight' => $weight,
                'cep' => $cep,
                'address' => $address,
                'neighborhood' => $neighborhood,
                'city' => $city,
                'state' => $state,
                'crm' => $crm,
                'emergency_contact' => $emergencyContact,
                'notes' => $notes,
            ]);

            if ($created) {
                $message = '<div style="padding:12px; background:#dcfce7; color:#166534; border-radius:10px; margin-bottom:16px;">Médico criado com sucesso e salvo diretamente no banco de dados do sistema.</div>';
            } else {
                $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Já existe um médico com este e-mail.</div>';
            }
        }
    }

    if ($action === 'create_appointment') {
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
}

$doctors = get_doctors();
?>
<div style="margin-bottom: 28px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Gerenciar Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Cadastre novos médicos, agende consultas e acompanhe a agenda dos profissionais.</p>
</div>

<?php echo $message; ?>

<div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:24px;">
    <button type="button" class="tab-doctor-button active" data-tab="agendar" style="padding:12px 18px; background:#851e32; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:600;">Agendar consulta</button>
    <button type="button" class="tab-doctor-button" data-tab="cadastro" style="padding:12px 18px; background:#e2e8f0; color:#1e2a3a; border:none; border-radius:10px; cursor:pointer; font-weight:600;">Cadastro médico</button>
    <button type="button" class="tab-doctor-button" data-tab="listagem" style="padding:12px 18px; background:#e2e8f0; color:#1e2a3a; border:none; border-radius:10px; cursor:pointer; font-weight:600;">Médicos cadastrados</button>
</div>

<div id="doctor-panel-agendar" class="doctor-panel" style="display:block; background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
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
        <input type="text" name="cpf" placeholder="CPF do paciente" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="phone" placeholder="Telefone do paciente" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="date" name="date" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="time" name="time" value="09:00" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <select name="status" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
            <option value="Pendente">Pendente</option>
            <option value="Confirmada">Confirmada</option>
            <option value="Recusada">Recusada</option>
        </select>
        <textarea name="symptoms" rows="3" placeholder="Sintomas / observações" style="grid-column: 1 / -1; padding:12px; border:1px solid #ddd; border-radius:10px; resize:vertical;"></textarea>
        <button type="submit" style="grid-column:1 / -1; padding:12px; background:#851e32; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer;">Agendar consulta</button>
    </form>
</div>

<div id="doctor-panel-cadastro" class="doctor-panel" style="display:none; background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h4 style="margin-bottom:18px; color:#1e2a3a;">Cadastro de médico</h4>
    <form method="post" style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:12px;">
        <input type="hidden" name="action" value="create_doctor">
        <input type="text" name="name" placeholder="Nome completo" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="cpf" placeholder="CPF" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="email" name="email" placeholder="E-mail" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="password" name="password" placeholder="Senha inicial" required style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="specialty" placeholder="Especialidade" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="crm" placeholder="CRM" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="date" name="birth_date" placeholder="Aniversário" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="number" name="age" min="0" placeholder="Idade" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="height" placeholder="Altura (cm)" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="weight" placeholder="Peso (kg)" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="phone" placeholder="Telefone" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="cep" placeholder="CEP" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="address" placeholder="Endereço" style="padding:12px; border:1px solid #ddd; border-radius:10px; grid-column: 1 / -1;">
        <input type="text" name="neighborhood" placeholder="Bairro" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="city" placeholder="Cidade" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="state" placeholder="UF" style="padding:12px; border:1px solid #ddd; border-radius:10px;">
        <input type="text" name="emergency_contact" placeholder="Contato de emergência" style="padding:12px; border:1px solid #ddd; border-radius:10px; grid-column: 1 / -1;">
        <textarea name="notes" rows="3" placeholder="Observações adicionais / descrição do médico" style="grid-column: 1 / -1; padding:12px; border:1px solid #ddd; border-radius:10px; resize:vertical;"></textarea>
        <button type="submit" style="grid-column:1 / -1; padding:12px; background:#851e32; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer;">Salvar médico</button>
    </form>
</div>

<div id="doctor-panel-listagem" class="doctor-panel" style="display:none; background:white; padding:24px; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <h4 style="margin-bottom:18px; color:#1e2a3a;">Médicos cadastrados</h4>
    <?php if (empty($doctors)) : ?>
        <p style="color:#64748b;">Nenhum médico cadastrado ainda.</p>
    <?php else : ?>
        <div style="display:grid; gap:16px;">
            <?php foreach ($doctors as $doctor) : ?>
                <div style="border:1px solid #f0f0f0; border-radius:14px; padding:18px; display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700; color:#1e2a3a; font-size:18px; margin-bottom:6px;">
                            <?php echo htmlspecialchars($doctor['name'] ?? 'Médico'); ?>
                        </div>
                        <div style="color:#64748b; font-size:13px; margin-bottom:4px;">Especialidade: <?php echo htmlspecialchars($doctor['specialty'] ?? 'Não informada'); ?></div>
                        <div style="color:#64748b; font-size:13px; margin-bottom:4px;">E-mail: <?php echo htmlspecialchars($doctor['email'] ?? ''); ?></div>
                        <div style="color:#64748b; font-size:13px; margin-bottom:4px;">CPF: <?php echo htmlspecialchars($doctor['cpf'] ?? 'Não informado'); ?></div>
                        <div style="color:#64748b; font-size:13px;">CRM: <?php echo htmlspecialchars($doctor['crm'] ?? 'Não informado'); ?></div>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button type="button" data-doctor-agenda="<?php echo htmlspecialchars($doctor['name'] ?? ''); ?>" style="padding:10px 14px; background:#851e32; color:white; border:none; border-radius:9px; cursor:pointer; font-weight:600;">Ver agenda</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div id="doctor-agenda-modal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:9999; align-items:center; justify-content:center; padding:24px;">
    <div style="background:white; border-radius:18px; width:min(760px, 92vw); max-height:82vh; overflow-y:auto; padding:24px; position:relative;">
        <button type="button" id="doctor-agenda-close" style="position:absolute; top:18px; right:18px; width:32px; height:32px; border:none; border-radius:999px; background:#e2e8f0; cursor:pointer; font-size:22px; color:#1e2a3a;">×</button>
        <h4 id="doctor-agenda-title" style="margin:0 0 18px; color:#1e2a3a; font-size:22px;">Agenda do médico</h4>
        <div id="doctor-agenda-content"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.tab-doctor-button');
        const panels = document.querySelectorAll('.doctor-panel');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const section = button.dataset.tab;
                buttons.forEach(function (item) {
                    item.classList.toggle('active', item === button);
                    item.style.background = item === button ? '#851e32' : '#e2e8f0';
                    item.style.color = item === button ? '#fff' : '#1e2a3a';
                });

                panels.forEach(function (panel) {
                    panel.style.display = panel.id === 'doctor-panel-' + section ? 'block' : 'none';
                });
            });
        });

        const closeBtn = document.getElementById('doctor-agenda-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                document.getElementById('doctor-agenda-modal').style.display = 'none';
            });
        }

        const modal = document.getElementById('doctor-agenda-modal');
        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    });

    async function openDoctorAgenda(doctorName) {
        if (!doctorName) return;

        const modal = document.getElementById('doctor-agenda-modal');
        const title = document.getElementById('doctor-agenda-title');
        const content = document.getElementById('doctor-agenda-content');

        if (!modal || !title || !content) return;

        title.textContent = 'Agenda de ' + doctorName;
        content.innerHTML = '<p style="color:#64748b; text-align:center; padding:30px;">Carregando agenda...</p>';
        modal.style.display = 'flex';

        try {
            const response = await fetch('../api/admin_api.php?action=get_agenda');
            const agenda = await response.json();
            const doctorAgenda = (Array.isArray(agenda) ? agenda : []).filter(function (item) {
                return (item.medico || '') === doctorName;
            });

            if (!doctorAgenda.length) {
                content.innerHTML = '<p style="color:#64748b; text-align:center; padding:20px;">Nenhuma consulta cadastrada para este médico.</p>';
                return;
            }

            const grouped = {};
            doctorAgenda.forEach(function (item) {
                const day = item.data_consulta || '';
                if (!day) return;
                if (!grouped[day]) grouped[day] = [];
                grouped[day].push(item);
            });

            const dates = Object.keys(grouped).sort();
            const html = ['<div style="display:grid; gap:12px;">'];
            dates.forEach(function (dateKey) {
                const dayItems = grouped[dateKey];
                const count = dayItems.length;
                html.push('<button type="button" data-doctor-name="' + doctorName + '" data-day-date="' + dateKey + '" style="text-align:left; display:flex; justify-content:space-between; align-items:center; padding:14px 16px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc; cursor:pointer;">');
                html.push('<span><strong>' + formatarData(dateKey) + '</strong></span>');
                html.push('<span style="background:#851e32; color:white; padding:5px 10px; border-radius:999px; font-size:12px; font-weight:600;">' + count + ' consulta(s)</span>');
                html.push('</button>');
            });
            html.push('</div>');
            content.innerHTML = html.join('');
        } catch (error) {
            content.innerHTML = '<p style="color:#b91c1c; text-align:center; padding:20px;">Erro ao carregar a agenda do médico.</p>';
        }
    }

    async function openDoctorAgendaDay(doctorName, dateKey) {
        if (!doctorName || !dateKey) return;

        const modal = document.getElementById('doctor-agenda-modal');
        const title = document.getElementById('doctor-agenda-title');
        const content = document.getElementById('doctor-agenda-content');

        if (!modal || !title || !content) return;

        title.textContent = 'Agenda de ' + doctorName + ' • ' + formatarData(dateKey);
        content.innerHTML = '<p style="color:#64748b; text-align:center; padding:30px;">Carregando horários...</p>';

        try {
            const response = await fetch('../api/admin_api.php?action=get_agenda_detalhes&medico=' + encodeURIComponent(doctorName) + '&data=' + encodeURIComponent(dateKey));
            const slots = await response.json();

            if (!Array.isArray(slots) || !slots.length) {
                content.innerHTML = '<p style="color:#64748b; text-align:center; padding:20px;">Nenhum horário registrado para este dia.</p>';
                return;
            }

            let html = '<div style="display:grid; gap:12px;">';
            slots.forEach(function (slot) {
                const time = slot.hora || '00:00';
                if (slot.ocupado) {
                    html += '<div style="padding:16px; border:1px solid #f1f5f9; background:#fff7ed; border-radius:12px;">';
                    html += '<div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:8px;">';
                    html += '<strong style="font-size:16px; color:#1e2a3a;">' + time + '</strong>';
                    html += '<span style="background:#851e32; color:white; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">' + (slot.status || 'Agendado') + '</span>';
                    html += '</div>';
                    html += '<div style="color:#1e2a3a; font-weight:600; margin-bottom:4px;">Paciente: ' + (slot.paciente_nome || 'Paciente') + '</div>';
                    html += '<div style="color:#64748b; font-size:13px;">Observação: ' + (slot.symptoms || 'Sem observações.') + '</div>';
                    html += '</div>';
                } else {
                    html += '<div style="padding:16px; border:1px solid #e2e8f0; background:#f8fafc; border-radius:12px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">';
                    html += '<strong style="font-size:16px; color:#1e2a3a;">' + time + '</strong>';
                    html += '<span style="background:#e2e8f0; color:#475569; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;">Sem paciente</span>';
                    html += '</div>';
                }
            });
            html += '</div>';
            content.innerHTML = html;
        } catch (error) {
            content.innerHTML = '<p style="color:#b91c1c; text-align:center; padding:20px;">Erro ao carregar os horários do dia.</p>';
        }
    }

    function formatarData(data) {
        if (!data) return 'Data indisponível';
        const d = new Date(data + 'T00:00:00');
        return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    document.addEventListener('click', function (event) {
        const doctorAgendaButton = event.target.closest('[data-doctor-agenda]');
        if (doctorAgendaButton) {
            openDoctorAgenda(doctorAgendaButton.dataset.doctorAgenda);
        }

        const dayButton = event.target.closest('[data-day-date]');
        if (dayButton) {
            openDoctorAgendaDay(dayButton.dataset.doctorName, dayButton.dataset.dayDate);
        }
    });
</script>
