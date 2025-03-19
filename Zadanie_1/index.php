<?php

/**
 * Format word by randomize character positions in it without first and last character.
 *
 * @param string $word
 * @return void
 */
function formatWord(string &$word): void
{
    if (empty($word)) return;

    $wordLength = strlen($word);
    $lastCharacterIndex = $wordLength - 1;
    $specialCharacter = false;

    // Make sure to exclude punctuation marks.
    if (ctype_punct($word[$lastCharacterIndex])) {
        $lastCharacterIndex -= 1;
        $wordLength -= 1;
        $specialCharacter = true;
    }

    // Process only words longer than 3 characters
    if ($wordLength > 3) {
        $wordParts = str_split(substr($word, 1, $lastCharacterIndex - 1));
        shuffle($wordParts);
        $reorderedString = implode($wordParts);
        $newWord = "{$word[0]}{$reorderedString}{$word[$lastCharacterIndex]}";
        if ($specialCharacter) {
            $newWord .= $word[$lastCharacterIndex + 1];
        }

        $word = $newWord;
    }
}

/**
 * Convert file content in to randomized characters position in words. It saves result in file.
 *
 * @param string $filename
 * @return void
 */
function processFile(string $filename): void
{
    try {
        $fileLines = file(__DIR__ . DIRECTORY_SEPARATOR . "{$filename}");
        foreach ($fileLines as &$fileLine) {
            $words = explode(' ', trim($fileLine));

            foreach ($words as &$word) {
                $word = trim($word);
                formatWord($word);
            }

            $fileLine = implode(' ', $words);
            $fileLine .= "\n";
        }

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . "processed.txt", mb_convert_encoding($fileLines, 'UTF-8'));
    } catch (Exception $e) {
        print_r($e->getMessage());
    }
}

processFile('test.txt');