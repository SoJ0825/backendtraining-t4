<?php


namespace App\Core\Database;


interface CollectData
{
    const BASE_DISTRICTS = [
        '南區', '北區', '安平區', '左鎮區', '仁德區', '關廟區', '官田區', '麻豆區', '佳里區', '西港區', '七股區', '將軍區', '學甲區',
        '北門區', '新營區', '後壁區', '白河區', '東山區', '下營區', '柳營區', '鹽水區', '山上區', '安定區',
    ];

    public function showDistricts(): array;

    public function sumByYear($district = null): array;

    public function sumByMonth($district = null): array;
}