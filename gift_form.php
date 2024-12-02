<?php
$python_script = 'gift_selector.py';
$command = escapeshellcmd("python3 $python_script list");
$output = shell_exec($command);

$gifts = json_decode($output, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Selection Form</title>
    <script>
        async function submitForm() {
            const checkboxes = document.querySelectorAll('input[name="gifts"]:checked');
            let selectedGifts = [];

            checkboxes.forEach(checkbox => {
                selectedGifts.push(checkbox.value);
            });

            const giftsParam = selectedGifts.join(',');

            try {
                const response = await fetch('gift_processor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ gifts: giftsParam })
                });

                if (!response.ok) {
                    document.getElementById('result').innerHTML = `
                    <h1>Error</h1>
                    <p>Error in request while processing your request</p>
                `;
                }

                const result = await response.json();

                document.getElementById('result').innerHTML = `
                    <h1>Result</h1>
                    <p>Selected Gifts: ${result.selected_gifts}</p>
                    <p>Unique Gift Code: ${result.unique_code}</p>
                `;
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('result').innerHTML = `
                    <h1>Error</h1>
                    <p>An error occurred while processing your request.</p>
                `;
            }
        }
    </script>
</head>
<body>
    <h1>Gift Selection</h1>
    <form onsubmit="event.preventDefault(); submitForm();">
        <p>Select your gifts:</p>
        <?php foreach ($gifts as $gift): ?>
            <label>
                <input type="checkbox" name="gifts" value="<?= $gift['key'] ?>"> <?= htmlspecialchars($gift['value']) ?>
            </label><br>
        <?php endforeach; ?>
        <br>
        <button type="submit">Submit</button>
    </form>

    <div id="result"></div>
</body>
</html>
