<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');

    $python_script = 'gift_selector.py';
    $command = "python3 $python_script";

    $process = proc_open($command, [
        0 => ["pipe", "r"], 
        1 => ["pipe", "w"], 
        2 => ["pipe", "w"]  
    ], $pipes);

    if (is_resource($process)) {
        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return_value = proc_close($process);

        if (!empty($errors)) {
            http_response_code(500);
            echo json_encode(['error' => $errors]);
        } else {
            header('Content-Type: application/json');
            echo $output;
        }
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Could not execute Python script.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
}
