<?php
//====================================Jawaban Soal Nomor 3.1=========================================

function combination(int $range, int $size, int $limiter=null) : Generator
{
    //Define Variabel
    $comb = array_fill(0, $size, 0);
    $stack = array(0);

    //Jika parameter terdapat parameter $limiter yang dikirim
    if ($limiter != null) {
        $range = $limiter;
    }

    while (count($stack) > 0) {
        $idx = count($stack) - 1;
        $val = array_pop($stack);

        while ($val < $range) {
            $comb[$idx++] = ++$val;
            array_push($stack, $val);
            //Jika panjang array sama dengan parameter yang dikirim maka di tambahkan
            //Break berfungsi agar tidak terdapat array yang sama
            if ($idx == $size) {
                yield $comb;
                break;
            }
        }
    }
}

function generateCombination(int $l, int $t) : array
{
    //Define Variabel
    $result = [];
    $comb = 9;
    
    //Mengulang Sebanyak Sembilan Kali dan Menambahkan Parameter l(panjang array) dan t(jumlah total array)
    foreach (combination($comb, $l, $t) as $value) {
        //Memassukan Array Jika Jumlahnya Sama Dengan parameter t
        if (array_sum($value) == $t) {
            array_push($result, $value);
        }
    }
    return $result;
}

//====================================Jawaban Soal Nomor 3.2.a=========================================
function biggestValue($value)
{
    //Mendifinisikan variabel $max dengan nilai default nilai pertama dari data array
    $max = $value[0];

    //Ulang sesuai dengan jumlah array
    for ($i = 0; $i < count($value); $i++) {
        //Ubah nilai $max jika nilai $max sebelumnya lebih kecil
        if ($max < $value[$i]) {
            $max = $value[$i];
        };
    };
    return $max;
}

//====================================Jawaban Soal Nomor 3.2.b=========================================
function anotherValue($value)
{
    //Mendifinisikan variabel $max dengan nilai default nilai pertama dari data array
    //$max digunakan untuk menyimpan angka terbesar dari array agar program lebih dinamis
    $max = $value[0];

    //Ulang sesuai dengan jumlah array
    for ($i = 0; $i < count($value); $i++) {
        //Ubah nilai $max jika nilai $max sebelumnya lebih kecil
        if ($max < $value[$i]) {
            $max = $value[$i];
        }
    }

    //Mendefinisikan nilai $index (berfungsi sebagai index array)
    $index = 0;

    //Mendefinisikan nilai $j (berfungsi sebagai angka yang urut sampai nilai sama dengan $max)
    $j = $value[0];
    while ($j < $max) {
        //Print nilai $j jika nilainya tidak sama dengan nilai yang ada didalam array
        if ($j != $value[$index]) {
            print($j);
        } else {
            //Tambah nilai $index jika nilainya sama dengan $j (ada di dalam array)
            $index++;
        }
        //Increment
        $j++;
    };
}

//====================================Jawaban Soal Nomor 3.2.c=========================================
function getLatestText($url) {
    //Regex Untuk Split URL
    $pattern = "/[-\s:]/";

    //Split URL to Array
    $components = preg_split($pattern, $url);

    //Mengambil index terakhir dari array (-1 karena index array dimulai dari 0)
    $result = $components[count($components)-1];

    return $result;
}

//==================================================================================================
//Nomor 3.1

$result = generateCombination(3,8);

print_r(json_encode($result));
print("\n");

//==================================================================================================
//Nomor 3.2.a
$value = [23, 76, 45, 20, 70, 65, 15, 54];
$maxValue = biggestValue($value);
print($maxValue);
print("\n");

//==================================================================================================
//Nomor 3.2.b
$value = [1, 2, 3, 5, 6, 7, 8, 9];
anotherValue($value);
print("\n");

//==================================================================================================
//Nomor 3.2.c
$url = "https://bola.okezone.com/read/2022/08/05/47/2642706/juventus-butuh-paul-pogba-ini-harapan-massimiliano-allegri";
$latestText = getLatestText($url);
print($latestText);
