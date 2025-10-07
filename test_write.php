<?php
$path = __DIR__ . "/uploads/sites/test.txt";

if (file_put_contents($path, "Hello World")) {
    echo "✅ Writable! File created at: $path";
} else {
    echo "❌ Not writable!";
}
