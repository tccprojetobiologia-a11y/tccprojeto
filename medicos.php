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
            $message = '<div style="padding:12px; background:#dcfce7; color:#166534; border-radius:10px; margin-bottom:16px;">Médico criado com sucesso. O acesso inicial é o e-mail e a senha informados.</div>';
        } else {
            $message = '<div style="padding:12px; background:#fee2e2; color:#b91c1c; border-radius:10px; margin-bottom:16px;">Já existe um médico com este e-mail.</div>';
        }
    }
}

$doctors = get_doctors();
?>
<div style="margin-bottom: 28px;">
    <h3 style="font-size: 24px; font-weight: 600; color: #1e2a3a;">Gerenciar Médicos</h3>
    <p style="color: #64748b; font-size: 14px; margin-top: 4px;">Cadastre novos médicos, defina um e-mail e uma senha inicial e deixe o perfil pronto para uso.</p>
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
