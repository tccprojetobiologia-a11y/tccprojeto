<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/auth_storage.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}
$doctorName = $data['doctor_name'] ?? '';
$patientName = $data['patient_name'] ?? '';
$patientAge = $data['patient_age'] ?? 0;
$cpf = $data['cpf'] ?? '';
$phone = $data['phone'] ?? '';
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';
$status = $data['status'] ?? 'Pendente';
$symptoms = $data['symptoms'] ?? '';

if (empty($doctorName) || empty($patientName) || empty($date)) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$doctor = find_doctor_by_name($doctorName);
if (!$doctor) {
    echo json_encode(['error' => 'Doctor not found in local store']);
    exit;
}
$doctorId = $doctor['id'] ?? '';

$created = create_appointment_record($doctorId, $patientName, $patientAge, $cpf, $phone, $symptoms, '', '', '', $status, $date, $time);
if ($created) {
    echo json_encode(['success' => true, 'appointment' => $created]);
} else {
    echo json_encode(['error' => 'Failed to create appointment in local store']);
}
