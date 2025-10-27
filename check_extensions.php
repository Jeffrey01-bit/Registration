<?php
echo "<h2>PHP Extensions Check</h2>";

// Check MongoDB extension
if (extension_loaded('mongodb')) {
    echo "✅ MongoDB extension is loaded<br>";
} else {
    echo "❌ MongoDB extension is NOT loaded<br>";
}

// Check Redis extension
if (extension_loaded('redis')) {
    echo "✅ Redis extension is loaded<br>";
} else {
    echo "❌ Redis extension is NOT loaded<br>";
}

// Check if classes exist
if (class_exists('MongoDB\Client')) {
    echo "✅ MongoDB\Client class exists<br>";
} else {
    echo "❌ MongoDB\Client class does NOT exist<br>";
}

if (class_exists('Redis')) {
    echo "✅ Redis class exists<br>";
} else {
    echo "❌ Redis class does NOT exist<br>";
}

// Show all loaded extensions
echo "<h3>All Loaded Extensions:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo $ext . "<br>";
}
?>