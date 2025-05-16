<?php
/*
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
$sql = "SELECT column_name, JSON_ARRAYAGG(REGEXP_SUBSTR(column_name, '\\[(.*?)\\]')) AS extracted_text FROM your_table GROUP BY column_name";
$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Original: " . $row['column_name'] . " - Extracted: " . $row['extracted_text'] . "\n";
}
*/
// Example of using the regex directly in PHP to extract all occurrences between [ and ]
$testString = '=06000000000$[0|caract2] [910000000|v_avoir] [1#] son actif [113#][32|a_seul@m_récompense], [=1|a_celui] [11|a_meilleur@m_musicien 1], [=2|a_remporté] [106#] [20|m_jeu] [38#] “[0|dis-titre]”FF';
preg_match_all('/\[(.*?)\]/', $testString, $matches, PREG_OFFSET_CAPTURE);

// Extract text that is not between [ and ]
$nonBracketText = preg_split('/\[[^\]]*\]/', $testString, -1, PREG_SPLIT_OFFSET_CAPTURE);
$filteredText = array_filter(array_map('trim', $nonBracketText), function ($text) {
    return !empty($text);
});

echo "Text outside brackets: " . implode(", ", $filteredText) . "\n";

if (!empty($matches[1])) {
    echo "Extracted values: " . implode(", ", $matches[1]) . "\n";
} else {
    echo "No matches found.\n";
}
