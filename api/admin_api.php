<?php
require_once __DIR__ . '/../config/auth_storage.php';

header('Content-Type: application/json; charset=utf-8');

function respondJson($payload, $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$rawBody = file_get_contents('php://input');
if (trim((string) $rawBody) !== '') {
    $decodedInput = json_decode($rawBody, true);
    if (is_array($decodedInput)) {
        $_POST = array_merge($_POST, $decodedInput);
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$store = get_auth_store();
$appointments = $store['appointments'] ?? [];
$doctors = get_doctors();

switch ($action) {
    case 'get_agenda':
        $agenda = [];
        foreach ($doctors as $doctor) {
            $doctorId = $doctor['id'] ?? '';
            $doctorAppointments = array_values(array_filter($appointments, function ($appointment) use ($doctorId) {
                return ($appointment['doctor_id'] ?? '') === $doctorId;
            }));

            foreach ($doctorAppointments as $appointment) {
                $agenda[] = [
                    'id' => $appointment['id'],
                    'medico' => $doctor['name'] ?? 'Médico',
                    'paciente_nome' => $appointment['patient_name'] ?? 'Paciente',
                    'data_consulta' => $appointment['date'] ?? '',
                    'hora_consulta' => $appointment['time'] ?? '09:00',
                    'status' => $appointment['status'] ?? 'Pendente',
                    'especialidade' => $doctor['specialty'] ?? 'Consulta',
                ];
            }
        }

        usort($agenda, function ($a, $b) {
            return strcmp(($a['data_consulta'] ?? '') . ' ' . ($a['hora_consulta'] ?? ''), ($b['data_consulta'] ?? '') . ' ' . ($b['hora_consulta'] ?? ''));
        });

        respondJson($agenda);
        break;

    case 'get_agenda_detalhes':
        $medico = trim((string) ($_GET['medico'] ?? ''));
        $data = trim((string) ($_GET['data'] ?? ''));

        if ($medico === '' || $data === '') {
            respondJson(['error' => 'Parâmetros inválidos.'], 400);
        }

        $doctor = find_doctor_by_name($medico);
        if (!$doctor) {
            respondJson(['error' => 'Médico não encontrado.'], 404);
        }

        $doctorAppointments = array_values(array_filter($appointments, function ($appointment) use ($doctor) {
            return ($appointment['doctor_id'] ?? '') === ($doctor['id'] ?? '');
        }));

        $filtered = array_values(array_filter($doctorAppointments, function ($appointment) use ($data) {
            return ($appointment['date'] ?? '') === $data;
        }));

        usort($filtered, function ($a, $b) {
            return strcmp(($a['time'] ?? '00:00'), ($b['time'] ?? '00:00'));
        });

        respondJson($filtered);
        break;

    case 'get_consultas':
        $consultas = array_map(function ($appointment) use ($doctors) {
            $doctor = null;
            foreach ($doctors as $item) {
                if (($item['id'] ?? '') === ($appointment['doctor_id'] ?? '')) {
                    $doctor = $item;
                    break;
                }
            }

            $status = trim((string) ($appointment['status'] ?? 'Pendente'));
            if ($status === 'Cancelada') {
                $status = 'Recusada';
            }
            if ($status === 'Realizada') {
                $status = 'Confirmada';
            }

            return [
                'id' => $appointment['id'] ?? '',
                'id_consulta' => $appointment['id'] ?? '',
                'doctor_id' => $appointment['doctor_id'] ?? '',
                'medico' => $doctor['name'] ?? 'Médico',
                'nome_medico' => $doctor['name'] ?? 'Médico',
                'especialidade' => $doctor['specialty'] ?? 'Consulta',
                'patient_name' => $appointment['patient_name'] ?? 'Paciente',
                'paciente_nome' => $appointment['patient_name'] ?? 'Paciente',
                'date' => $appointment['date'] ?? '',
                'data_consulta' => $appointment['date'] ?? '',
                'time' => $appointment['time'] ?? '09:00',
                'hora_consulta' => $appointment['time'] ?? '09:00',
                'status' => $status,
                'mensagem_recusa' => $appointment['mensagem_recusa'] ?? '',
                'symptoms' => $appointment['symptoms'] ?? '',
                'phone' => $appointment['phone'] ?? '',
                'cpf' => $appointment['cpf'] ?? '',
            ];
        }, $appointments);

        respondJson($consultas);
        break;

    case 'aprovar_consulta':
        $id = trim((string) ($_POST['id'] ?? ''));
        if ($id === '') {
            respondJson(['success' => false, 'message' => 'Consulta não informada.'], 400);
        }

        foreach ($appointments as &$appointment) {
            if (($appointment['id'] ?? '') === $id) {
                $appointment['status'] = 'Confirmada';
                $appointment['mensagem_recusa'] = '';
                $store['appointments'] = $appointments;
                save_auth_store($store);
                respondJson(['success' => true, 'message' => 'Consulta confirmada.']);
            }
        }

        respondJson(['success' => false, 'message' => 'Consulta não encontrada.'], 404);
        break;

    case 'recusar_consulta':
        $id = trim((string) ($_POST['id'] ?? ''));
        $mensagem = trim((string) ($_POST['mensagem'] ?? ''));
        if ($id === '') {
            respondJson(['success' => false, 'message' => 'Consulta não informada.'], 400);
        }
        if ($mensagem === '') {
            respondJson(['success' => false, 'message' => 'Informe a justificativa da recusa.'], 400);
        }

        foreach ($appointments as &$appointment) {
            if (($appointment['id'] ?? '') === $id) {
                $appointment['status'] = 'Recusada';
                $appointment['mensagem_recusa'] = $mensagem;
                $store['appointments'] = $appointments;
                save_auth_store($store);
                respondJson(['success' => true, 'message' => 'Consulta recusada com justificativa.']);
            }
        }

        respondJson(['success' => false, 'message' => 'Consulta não encontrada.'], 404);
        break;

    case 'create_appointment':
        $doctorId = trim((string) ($_POST['doctor_id'] ?? ''));
        $patientName = trim((string) ($_POST['patient_name'] ?? ''));
        $patientAge = (int) ($_POST['patient_age'] ?? 0);
        $cpf = trim((string) ($_POST['cpf'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $date = trim((string) ($_POST['date'] ?? ''));
        $time = trim((string) ($_POST['time'] ?? '09:00'));
        $status = trim((string) ($_POST['status'] ?? 'Pendente'));
        $symptoms = trim((string) ($_POST['symptoms'] ?? 'Consulta agendada pelo admin.'));

        if ($doctorId === '' || $patientName === '' || $date === '') {
            respondJson(['success' => false, 'message' => 'Preencha médico, paciente e data.'], 400);
        }

        $created = create_appointment_record(
            $doctorId,
            $patientName,
            $patientAge,
            $cpf,
            $phone,
            $symptoms,
            '',
            '',
            '',
            $status,
            $date,
            $time
        );

        respondJson(['success' => true, 'message' => 'Consulta agendada com sucesso.', 'appointment' => $created]);
        break;

    case 'get_medicos':
        respondJson($doctors);
        break;

    case 'get_pacientes':
        $pacientes = [];
        foreach ($appointments as $appointment) {
            $pacientes[] = [
                'id' => $appointment['id'],
                'nome' => $appointment['patient_name'] ?? 'Paciente',
                'status' => $appointment['status'] ?? 'Pendente',
                'data' => $appointment['date'] ?? '',
                'hora' => $appointment['time'] ?? '09:00',
            ];
        }
        respondJson($pacientes);
        break;

    default:
        respondJson(['error' => 'Ação inválida.'], 400);
        break;
}
