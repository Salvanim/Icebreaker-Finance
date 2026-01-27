<?php
function pyScript($python_file, $params) {
    // Ensure the file exists
    if (!file_exists($python_file)) {
        return "Error: Python file does not exist.";
    }

    // Escape parameters to prevent command injection and handle spaces
    $escaped_params = array_map('escapeshellarg', $params);

    // Build the command string
    $command = "python " . escapeshellarg($python_file) . " " . implode(" ", $escaped_params);

    // Execute and capture output and errors
    $process = proc_open(
        $command,
        [1 => ["pipe", "w"], 2 => ["pipe", "w"]],
        $pipes
    );

    if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);

        // Return both output and error
        return nl2br(implode("\n", [
            "output" => trim($output),
            "error" => trim($error)
        ]));
    } else {
        return "Error: Failed to execute Python script.";
    }
}
?>
