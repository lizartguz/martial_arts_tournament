<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentS extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idBeni = DB::table('departments')->insertGetId([
            'name' => 'BENI',
            'abbreviation' => 'BEN',
        ]);
        //CERCADO
        $idCercadoBeni = DB::table('provinces')->insertGetId([
            'name' => 'CERCADO',
            'department_id' => 1
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TRINIDAD',
            'province_id' => $idCercadoBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN JAVIER',
            'province_id' => $idCercadoBeni,




        ]);

        //VACA DIEZ
        $idVacaDiezBeni = DB::table('provinces')->insertGetId([
            'name' => 'VACA DIEZ',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'RIBERALTA',
            'province_id' => $idVacaDiezBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'GUAYARAMERIN',
            'province_id' => $idVacaDiezBeni,




        ]);

        //JOSE VALLIVIAN
        $idJoseBallivianBeni = DB::table('provinces')->insertGetId([
            'name' => 'JOSE BALLIVIAN',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'REYES',
            'province_id' => $idJoseBallivianBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'RURRENABAQUE',
            'province_id' => $idJoseBallivianBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN BORJA',
            'province_id' => $idJoseBallivianBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTA ROSA',
            'province_id' => $idJoseBallivianBeni,




        ]);

        //YACUMA
        $idYacumaBeni = DB::table('provinces')->insertGetId([
            'name' => 'YACUMA',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTA ANA',
            'province_id' => $idYacumaBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EXALTACION',
            'province_id' => $idYacumaBeni,




        ]);

        //MOXOS
        $idMoxosBeni = DB::table('provinces')->insertGetId([
            'name' => 'MOXOS',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN INGNACIO',
            'province_id' => $idMoxosBeni,




        ]);
        //MARBAN
        $idMarbanBeni = DB::table('provinces')->insertGetId([
            'name' => 'MARBAN',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LORETO',
            'province_id' => $idMarbanBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN ANDRES',
            'province_id' => $idMarbanBeni,




        ]);
        //MAMORE
        $idMamoreBeni = DB::table('provinces')->insertGetId([
            'name' => 'MAMORE',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN JOAQUIN',
            'province_id' => $idMamoreBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUESTO SILES',
            'province_id' => $idMamoreBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN RAMON',
            'province_id' => $idMamoreBeni,




        ]);
        //ITENEZ
        $idItenezBeni = DB::table('provinces')->insertGetId([
            'name' => 'ITENEZ',
            'department_id' => $idBeni,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MAGDALENA',
            'province_id' => $idItenezBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BAURES',
            'province_id' => $idItenezBeni,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUARACAJE',
            'province_id' => $idItenezBeni,




        ]);




        //CHUQUISACA - ID 1
        $idChuquisaca = DB::table('departments')->insertGetId([
            'name' => 'CHUQUISACA',
            'abbreviation' => 'CHQ',
        ]);
        //AZURDUY
        $idAzurduyChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'AZURDUY',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA AZURDUY',
            'province_id' => $idAzurduyChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TARVITA',
            'province_id' => $idAzurduyChuquisaca,




        ]);
        //BELISARIO BOETO
        $idBelisarioBoetoChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'BELISARIO BOETO',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA SERRANO',
            'province_id' => $idBelisarioBoetoChuquisaca,




        ]);
        //HERNANDO SILES
        $idHernandoSilesChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'HERNANDO SILES',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MONTEAGUDO',
            'province_id' => $idHernandoSilesChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PABLO DE HUACARETA',
            'province_id' => $idHernandoSilesChuquisaca,




        ]);
        //LUIS VALVO
        $idLuisCalvoChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'LUIS CALVO',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA VACA GUZMAN',
            'province_id' => $idLuisCalvoChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUACAYA',
            'province_id' => $idLuisCalvoChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MACHARETI',
            'province_id' => $idLuisCalvoChuquisaca,




        ]);
        //NOR CINTI
        $idNorCintiChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'NOR CINTI',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAMARGO',
            'province_id' => $idNorCintiChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'INCAHUASI',
            'province_id' => $idNorCintiChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN LUCAS',
            'province_id' => $idNorCintiChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA CHARCAS',
            'province_id' => $idNorCintiChuquisaca,




        ]);
        //OROPEZA
        $idOropezachuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'OROPEZA',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SUCRE',
            'province_id' => $idOropezachuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'POROMA',
            'province_id' => $idOropezachuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'YOTALA',
            'province_id' => $idOropezachuquisaca,




        ]);
        //SUD CINTI
        $idSudCintiChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'SUD CINTI',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAMATAQUI (VILLA ABECIA)',
            'province_id' => $idSudCintiChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CULPINA',
            'province_id' => $idSudCintiChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'LAS CARRERAS',
            'province_id' => $idSudCintiChuquisaca,




        ]);
        //TOMINA
        $idTominaChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'TOMINA',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PADILLA',
            'province_id' => $idTominaChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL VILLAR',
            'province_id' => $idTominaChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SOPACHUY',
            'province_id' => $idTominaChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOMINA',
            'province_id' => $idTominaChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA ALCALA',
            'province_id' => $idTominaChuquisaca,




        ]);
        //YAMPARAEZ
        $idYamparaezChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'YAMPARAEZ',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TARABUCO',
            'province_id' => $idYamparaezChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'YAMPARAEZ',
            'province_id' => $idYamparaezChuquisaca,




        ]);
        //ZUDAÑEZ
        $idZudanezChuquisaca = DB::table('provinces')->insertGetId([
            'name' => 'ZUDAÑES',
            'department_id' => $idChuquisaca,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA ZUDAÑEZ',
            'province_id' => $idZudanezChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ICLA',
            'province_id' => $idZudanezChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PRESTO',
            'province_id' => $idZudanezChuquisaca,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA MOJOCOYA',
            'province_id' => $idZudanezChuquisaca,
        ]);

        //COCHABAMBA - ID 3
        $idCochabamba = DB::table('departments')->insertGetId([
            'name' => 'COCHABAMBA',
            'abbreviation' => 'CBA',        
        ]);
        //ARANI
        $idAraniCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'ARANI',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ARANI',
            'province_id' => $idAraniCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VACAS',
            'province_id' => $idAraniCochabamba,




        ]);
        //ARQUE
        $idArqueCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'ARQUE',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ARQUE',
            'province_id' => $idArqueCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TACOPAYA',
            'province_id' => $idArqueCochabamba,




        ]);
        //AYOPAYA
        $idAyopayaCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'AYOPAYA',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'INDEPENDENCIA',
            'province_id' => $idAyopayaCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MOROCHATA',
            'province_id' => $idAyopayaCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COCAPATA',
            'province_id' => $idAyopayaCochabamba,




        ]);
        //BOLIVAR
        $idBolivarCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'BOLIVAR',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'BOLIVAR',
            'province_id' => $idBolivarCochabamba,




        ]);
        //CAMPERO
        $idCamperoCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'CAMPERO',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ALQUILE',
            'province_id' => $idCamperoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'OMEREQUE',
            'province_id' => $idCamperoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PASORAPA',
            'province_id' => $idCamperoCochabamba,




        ]);
        //CAPINOTA
        $idCapinotaCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'CAPINOTA',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAPINOTA',
            'province_id' => $idCapinotaCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIVAÑEZ',
            'province_id' => $idCapinotaCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SICAYA',
            'province_id' => $idCapinotaCochabamba,




        ]);
        //CARRASCO
        $idCarrascoCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'CARRASCO',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOTORA',
            'province_id' => $idCarrascoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHIMORE',
            'province_id' => $idCarrascoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ENTRE RIOS',
            'province_id' => $idCarrascoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'POCONA',
            'province_id' => $idCarrascoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'POJO',
            'province_id' => $idCarrascoCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO VILLARROEL',
            'province_id' => $idCarrascoCochabamba,




        ]);
        //CERCADO
        $idCercadoCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'CERCADO',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COCHABAMBA',
            'province_id' => $idCercadoCochabamba,




        ]);
        //CHAPARE
        $idChapareCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'CHAPARE',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SACABA',
            'province_id' => $idChapareCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLOMI',
            'province_id' => $idChapareCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA TUNARI',
            'province_id' => $idChapareCochabamba,




        ]);
        //ARCE
        $idEstebanArceCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'ESTEBAN ARCE',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TARATA',
            'province_id' => $idEstebanArceCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ANZALDO',
            'province_id' => $idEstebanArceCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ARBIETO',
            'province_id' => $idEstebanArceCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SACABAMBA',
            'province_id' => $idEstebanArceCochabamba,




        ]);
        //JORDAN
        $idJordanCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'J0RDAN',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CLIZA',
            'province_id' => $idJordanCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOCO',
            'province_id' => $idJordanCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOLATA',
            'province_id' => $idJordanCochabamba,




        ]);
        //MIZQUE
        $idMizqueCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'MIZQUE',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MIZQUE',
            'province_id' => $idMizqueCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ALALAY',
            'province_id' => $idMizqueCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILA VILA',
            'province_id' => $idMizqueCochabamba,




        ]);
        //PUNATA
        $idPunataCochabamba =  DB::table('provinces')->insertGetId([
            'name' => 'PUNATA',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUNATA',
            'province_id' => $idPunataCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CUCHUMUELA',
            'province_id' => $idPunataCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN BENITO',
            'province_id' => $idPunataCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TACACHI',
            'province_id' => $idPunataCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA RIVERO',
            'province_id' => 31
        ]);
        //QUILLACOLLO
        $idQuillacolloCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'QUILLACOLLO',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'QUILLACOLLO',
            'province_id' =>  $idQuillacolloCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLCAPIRHUA',
            'province_id' =>  $idQuillacolloCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SIPE SIPE',
            'province_id' =>  $idQuillacolloCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TIQUIPAYA',
            'province_id' =>  $idQuillacolloCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VINTO',
            'province_id' =>  $idQuillacolloCochabamba,




        ]);
        //TAPACARI
        $idTapacariCochabamba = DB::table('provinces')->insertGetId([
            'name' => 'TAPACARI',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TAPACARI',
            'province_id' => $idTapacariCochabamba,




        ]);
        //TIRAQUE
        $idTiraqueCochabamba =  DB::table('provinces')->insertGetId([
            'name' => 'TIRAQUE',
            'department_id' => $idCochabamba,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TIRAQUE',
            'province_id' => $idTiraqueCochabamba,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SHINAHOTA',
            'province_id' => $idTiraqueCochabamba,




        ]);

        //LA PAZ - ID 4

        $idLaPaz = DB::table('departments')->insertGetId([
            'name' => 'LA PAZ',
            'abbreviation' => 'LPZ',
        ]);
        //Abel Iturralde
        $idAbelIturraldeLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'ABEL ITURRALDE',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'IXIAMAS',
            'province_id' => $idAbelIturraldeLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN BUENAVENTURA',
            'province_id' => $idAbelIturraldeLaPaz,




        ]);
        // AROMA
        $idAromaLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'AROMA',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SICA SICA',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'AYO AYO',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CALAMARCA',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLLANA',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLQUENCHA',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PATACAMAYA',
            'province_id' => $idAromaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'UMALA',
            'province_id' => $idAromaLaPaz,




        ]);
        // BAUTISTA SAAVEDRA
        $idBautistasaavedraLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'BAUTISTA SAAVEDRA',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'GENERAL JUAN JOSE PEREZ (CHARAZAN)',
            'province_id' => $idBautistasaavedraLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CURVA',
            'province_id' => $idBautistasaavedraLaPaz,




        ]);
        // CAMACHO
        $idCamachoLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'CAMACHO',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO ACOSTA',
            'province_id' => $idCamachoLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MOCOMOCO',
            'province_id' => $idCamachoLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO CARABUCO',
            'province_id' => $idCamachoLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ESCOMA',
            'province_id' => $idCamachoLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUMANATA',
            'province_id' => $idCamachoLaPaz,




        ]);
        // CARANAVI
        $idCaranaviLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'CARANAVI',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARANAVI',
            'province_id' => $idCaranaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ALTO BENI',
            'province_id' => $idCaranaviLaPaz,




        ]);
        // FRANZ TAMYO
        $idFranzTamayo = DB::table('provinces')->insertGetId([
            'name' => 'FRANZ TAMAYO',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'APOLO',
            'province_id' => $idFranzTamayo,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PALECHUCO',
            'province_id' => $idFranzTamayo,




        ]);
        // GUALBERTO VILLARROEL
        $idGualbertoVillarroel = DB::table('provinces')->insertGetId([
            'name' => 'GUALBERTO VILLARROEL',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO DE CUARAHUARA',
            'province_id' => $idGualbertoVillarroel,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHACARILLA',
            'province_id' => $idGualbertoVillarroel,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PAPEL PAMPA',
            'province_id' => $idGualbertoVillarroel,




        ]);
        // INGAVI
        $idIngaviLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'INGAVI',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VIACHA',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'DESAGUADERO',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'GUAQUI',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'JESUS DE MACHACA',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN ANDRES DE MACHACA',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TARACO',
            'province_id' => $idIngaviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TIAHUANACO',
            'province_id' => $idIngaviLaPaz,




        ]);
        // INQUISIVI
        $idInquisiviLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'INQUISIVI',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'INSQUISIVI',
            'province_id' => $idInquisiviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAJUATA',
            'province_id' => $idInquisiviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLQUIRI',
            'province_id' => $idInquisiviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ICHOCA',
            'province_id' => $idInquisiviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'LICOMA PAMPA',
            'province_id' => $idInquisiviLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'QUIME',
            'province_id' => $idInquisiviLaPaz,




        ]);
        // JOSE MANUEL PANDO
        $idJoseManuelPando = DB::table('provinces')->insertGetId([
            'name' => 'JOSE MANUEL PANDO',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE MACHACA',
            'province_id' => $idJoseManuelPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CATACORA',
            'province_id' => $idJoseManuelPando,




        ]);
        // LARACAJA
        $idLaracajaLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'LARECAJA',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SORATA',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COMBAYA',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'GUANAY',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MAPIRI',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'QUIABAYA',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TACACOMA',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TEOPONTE',
            'province_id' => $idLaracajaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TIPUANI',
            'province_id' => $idLaracajaLaPaz,




        ]);
        //LOAYZA
        $idLoayzaLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'LOAYZA',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LUIRBAY',
            'province_id' => $idLoayzaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAIROMA',
            'province_id' => $idLoayzaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MALLA',
            'province_id' => $idLoayzaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAPAHAQUI',
            'province_id' => $idLoayzaLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'YACO',
            'province_id' => $idLoayzaLaPaz,




        ]);
        //LO ANDES
        $idLosAndesLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'PROVINCIA DE LOS ANDES',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUCARANI',
            'province_id' => $idLosAndesLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BATALLAS',
            'province_id' => $idLosAndesLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'LAJA',
            'province_id' => $idLosAndesLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO PEREZ',
            'province_id' => $idLosAndesLaPaz,




        ]);
        //MANCOKAPAC
        $idMancoKapacLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'MANCO KAPAC',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COPACABANA',
            'province_id' => $idMancoKapacLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO DE TIQUINA',
            'province_id' => $idMancoKapacLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TITO YUPANQUI',
            'province_id' => $idMancoKapacLaPaz,




        ]);
        //MUÑECAS
        $idmunecasLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'MUÑECAS',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHUMA',
            'province_id' =>  $idmunecasLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'AUCAPADA',
            'province_id' =>  $idmunecasLaPaz,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'AYATA',
            'province_id' =>  $idmunecasLaPaz,

        ]);
        //MURILLO
        $idMurilloLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'MURILLO',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PALCA',
            'province_id' => $idMurilloLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ACHOCALLA',
            'province_id' => $idMurilloLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL ALTO',
            'province_id' => $idMurilloLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LA PAZ',
            'province_id' => $idMurilloLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MECAPACA',
            'province_id' => $idMurilloLaPaz,
        ]);
        //NOR YUNGAS
        $idNorYungasLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'NOR YUNGAS',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COROICO',
            'province_id' => $idNorYungasLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CORIPATA',
            'province_id' => $idNorYungasLaPaz,
        ]);
        //OMASUYOS
        $idOmasuyosLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'OMASUYOS',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ACHACACHI',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ANCORAIMES',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHUA COCANI',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUARINA',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUATAJATA',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE HUATA',
            'province_id' => $idOmasuyosLaPaz,
        ]);
        //PACAJES
        $idPacajesLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'PACAJES',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CORO CORO',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLACOTO',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAQUILAVIRI',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHARAÑA',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COMANCHE',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'NAZACARA DE PACAJES',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE CALLAPA',
            'province_id' => $idPacajesLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'WALDO BALLIVIAN',
            'province_id' => $idPacajesLaPaz,
        ]);
        //SUD YUNGAS
        $idSudYungasLaPaz = DB::table('provinces')->insertGetId([
            'name' => 'SUD YUNGAS',
            'department_id' => $idLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHULUMARI',
            'province_id' => $idSudYungasLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'IRUPANA',
            'province_id' => $idSudYungasLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LA ASUNTA',
            'province_id' => $idSudYungasLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PALOS BLANCOS',
            'province_id' => $idSudYungasLaPaz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'YANACACHI',
            'province_id' => $idSudYungasLaPaz,
        ]);



        //ORURO - ID 5
        $idOruro = DB::table('departments')->insertGetId([
            'name' => 'ORURO',
            'abbreviation' => 'ORU',
        ]);
        //SABAYA
        $idSabayaOruro = DB::table('provinces')->insertGetId([
            'name' => 'SABAYA',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SABAYA',
            'province_id' => $idSabayaOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHIPAYA',
            'province_id' => $idSabayaOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COIPASA',
            'province_id' => $idSabayaOruro,
        ]);
        // CARANGAS
        $idCarangasOruro = DB::table('provinces')->insertGetId([
            'name' => 'CARANGAS',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CORQUE',
            'province_id' => $idCarangasOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHOQUECOTA',
            'province_id' => $idCarangasOruro,
        ]);
        // CERCADO
        $idCercadoOruro = DB::table('provinces')->insertGetId([
            'name' => 'CERCADO',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ORURO',
            'province_id' => $idCercadoOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARACOLLO',
            'province_id' => $idCercadoOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL CHORO',
            'province_id' => $idCercadoOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SORACACHI',
            'province_id' => $idCercadoOruro,
        ]);
        // EDUARDO AVAROA
        $idEduardoAvaroa = DB::table('provinces')->insertGetId([
            'name' => 'EDUARDO ABAROA',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHALLAPATA',
            'province_id' => $idEduardoAvaroa,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTUARIO DE QUILLACAS',
            'province_id' => $idEduardoAvaroa,
        ]);
        // LADISLAO CABRERA
        $idLadislaoCabrera = DB::table('provinces')->insertGetId([
            'name' => 'LADISLAO CABRERA',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SALINAS DE GARCIA MENDOZA',
            'province_id' => $idLadislaoCabrera,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PAMPA AULLAGAS',
            'province_id' => $idLadislaoCabrera,
        ]);
        // LITORAL
        $idLitoralOruro = DB::table('provinces')->insertGetId([
            'name' => 'LITORAL',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'HUACHACALLA',
            'province_id' => $idLitoralOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CRUZ DE MACHACAMARCA',
            'province_id' => $idLitoralOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ESCARA',
            'province_id' => $idLitoralOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ESMERALDA',
            'province_id' => $idLitoralOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'YUNGUYO DEL LITORAL',
            'province_id' => $idLitoralOruro,
        ]);
        // MEJILLONES
        $idMejillonesOruro = DB::table('provinces')->insertGetId([
            'name' => 'MEJILLONES',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LA RIVERA',
            'province_id' => $idMejillonesOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARANGAS',
            'province_id' => $idMejillonesOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TODOS SANTOS',
            'province_id' => $idMejillonesOruro,
        ]);
        // NOR CARANGAS
        $idNorCarangas = DB::table('provinces')->insertGetId([
            'name' => 'NOR CARANGAS',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE HUAYLLAMARCAR',
            'province_id' => $idNorCarangas,
        ]);
        // PANTALEON DALENCE
        $idPantaleonDalence = DB::table('provinces')->insertGetId([
            'name' => 'PNATALEON DALENCE',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA HUANUNI',
            'province_id' => $idPantaleonDalence,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MACHACAMARCA',
            'province_id' => $idPantaleonDalence,
        ]);
        // POOPO
        $idPoopoOruro = DB::table('provinces')->insertGetId([
            'name' => 'POOPO',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA POOPO',
            'province_id' => $idPoopoOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ANTEQUERA',
            'province_id' => $idPoopoOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PAZÑA',
            'province_id' => $idPoopoOruro,
        ]);
        // SAJAMA
        $idSajamaOruro = DB::table('provinces')->insertGetId([
            'name' => 'SAJAMA',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARAHUARA DE CARANGAS',
            'province_id' => $idSajamaOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TURCO',
            'province_id' => $idSajamaOruro,
        ]);
        // PEDRO TOTORA
        $idPedroTotoraOruro = DB::table('provinces')->insertGetId([
            'name' => 'PEDRO DE TOTORA',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOTORA',
            'province_id' => $idPedroTotoraOruro,
        ]);
        // SAUCARI
        $idSaucariOruro = DB::table('provinces')->insertGetId([
            'name' => 'SAUCARI',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOLEDO',
            'province_id' => $idSaucariOruro,

        ]);
        // SEBASTIAN PAGADOR
        $idSebastianPagadorOruro = DB::table('provinces')->insertGetId([
            'name' => 'SEBASTIAN PAGADOR',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE HUARI',
            'province_id' => $idSebastianPagadorOruro,




        ]);
        // SUD CARANGAS
        $idSudCarangasOruro = DB::table('provinces')->insertGetId([
            'name' => 'SUD CARANGAS',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTIAGO DE ANDAMARCA',
            'province_id' => $idSudCarangasOruro,
        ]);

        DB::table('municipalities')->insert([
            'name' => 'BELEN DE ANDAMARCA',
            'province_id' => $idSudCarangasOruro,
        ]);
        // BARRON
        $idBarronOruro = DB::table('provinces')->insertGetId([
            'name' => 'BARRON',
            'department_id' => $idOruro,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'EUCALIPTUS',
            'province_id' => $idBarronOruro,




        ]);

        //PANDO id - 6
        $idPando = DB::table('departments')->insertGetId([
            'name' => 'PANDO',
            'abbreviation' => 'PND',
        ]);
        //SANTA ROSA DE ABUNA
        $idAbunaPando = DB::table('provinces')->insertGetId([
            'name' => 'SANTA ROSA DE ABUNA',
            'department_id' => $idPando,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTA ROSA DE ABUNA',
            'province_id' => $idAbunaPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'INGAVI',
            'province_id' => $idAbunaPando,




        ]);
        // FEDERICO ROMAN
        $idFedericoRomanPando = DB::table('provinces')->insertGetId([
            'name' => 'FEDERICO ROMAN',
            'department_id' => $idPando,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'NUEVA ESPERANZA',
            'province_id' => $idFedericoRomanPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTOS MERCADO',
            'province_id' => $idFedericoRomanPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA NUEVA',
            'province_id' => $idFedericoRomanPando,




        ]);
        // MADRE DE DIOS
        $idMadreDeDiosPando = DB::table('provinces')->insertGetId([
            'name' => 'MADRE DE DIOS',
            'department_id' => $idPando,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO GONZALO MORENO',
            'province_id' => $idMadreDeDiosPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN LORENZO',
            'province_id' => $idMadreDeDiosPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL SENA',
            'province_id' => $idMadreDeDiosPando,




        ]);
        // MANURIPI
        $idManuripiPando = DB::table('provinces')->insertGetId([
            'name' => 'MANURIPI',
            'department_id' => $idPando,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO RICO',
            'province_id' => $idManuripiPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'FILADELFIA',
            'province_id' => $idManuripiPando,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO',
            'province_id' => $idManuripiPando,




        ]);
        //NICOLAS SUAREZ
        $idNicolasSuarez = DB::table('provinces')->insertGetId([
            'name' => 'NICOLAS SUAREZ',
            'department_id' => $idPando,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COBIJA',
            'province_id' => $idNicolasSuarez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PORVENIR',
            'province_id' => $idNicolasSuarez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BELLA FLOR',
            'province_id' => $idNicolasSuarez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BOLPEBRA',
            'province_id' => $idNicolasSuarez,




        ]);

        //POTOSI - ID 7
        $idPotosi = DB::table('departments')->insertGetId([
            'name' => 'POTOSI',
            'abbreviation' => 'PSI',
        ]);
        // ALONSO IBAÑEZ
        $idAlonsoIbanez = DB::table('provinces')->insertGetId([
            'name' => 'ALONSO IBAÑEZ',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SACACA',
            'province_id' => $idAlonsoIbanez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARIPUYO',
            'province_id' => $idAlonsoIbanez,




        ]);
        // ANTONIO QUIJARRO
        $idAntonioQuijarroPotosi = DB::table('provinces')->insertGetId([
            'name' => 'ANTONIO QUIJARRO',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'UYUNI',
            'province_id' => $idAntonioQuijarroPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PORCO',
            'province_id' => $idAntonioQuijarroPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TOMAVE',
            'province_id' => $idAntonioQuijarroPotosi,




        ]);
        // BERNARDINO BILBAO
        $idBernardinoBilbaoPotosi = DB::table('provinces')->insertGetId([
            'name' => 'BERNARDINO BILBAO',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ARAMPAMPA',
            'province_id' => $idBernardinoBilbaoPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ACASIO',
            'province_id' => $idBernardinoBilbaoPotosi,




        ]);
        // CHARCAS
        $idCharcasPotosi = DB::table('provinces')->insertGetId([
            'name' => 'CHARCAS',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO DE BUENA VISTA',
            'province_id' => $idCharcasPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TORO TORO',
            'province_id' => $idCharcasPotosi,




        ]);
        // CHAYANTA
        $idChayantaPotosi = DB::table('provinces')->insertGetId([
            'name' => 'CHAYANTA',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLQUECHACA',
            'province_id' =>  $idChayantaPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'OCURI',
            'province_id' =>  $idChayantaPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'POCOATA',
            'province_id' =>  $idChayantaPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'RAVELO',
            'province_id' =>  $idChayantaPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO DE MACHA',
            'province_id' =>  $idChayantaPotosi,




        ]);
        // CORNELIO SAAVEDRA
        $idCornelioSaavedraPotosi = DB::table('provinces')->insertGetId([
            'name' => 'CORNELIO SAAVEDRA',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'BETANZOS',
            'province_id' => $idCornelioSaavedraPotosi,




        ]);

        DB::table('municipalities')->insert([
            'name' => 'CHAQUI',
            'province_id' => $idCornelioSaavedraPotosi,




        ]);

        DB::table('municipalities')->insert([
            'name' => 'TACOBAMBA',
            'province_id' => $idCornelioSaavedraPotosi,




        ]);
        // DANIEL CAMPOS
        $idDanielCamposPotosi = DB::table('provinces')->insertGetId([
            'name' => 'DANIEL CAMPOS',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LLICA',
            'province_id' => $idDanielCamposPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TAHUA',
            'province_id' => $idDanielCamposPotosi,




        ]);
        // ENRIQUE BALDIVIESO
        $idEnriqueBaldiviesoPotosi = DB::table('provinces')->insertGetId([
            'name' => 'ENRIQUE BALDIVIESO',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN AGUSTIN',
            'province_id' => $idEnriqueBaldiviesoPotosi,




        ]);
        // JOSE MARIA LINARES
        $idJoseMariaLinaresPotosi = DB::table('provinces')->insertGetId([
            'name' => 'JOSE MARIA LINARES',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUNA',
            'province_id' => $idJoseMariaLinaresPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAIZA ',
            'province_id' => $idJoseMariaLinaresPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CKOCHAS',
            'province_id' => $idJoseMariaLinaresPotosi,




        ]);
        // MODESTO OMISTE
        $idModestoOmistePotosi = DB::table('provinces')->insertGetId([
            'name' => 'MODESTO OMISTE',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLAZON',
            'province_id' => $idModestoOmistePotosi,




        ]);
        // NOR CHICHAS
        $idNorLipezPotosi = DB::table('provinces')->insertGetId([
            'name' => 'NOR CHICHAS',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COTA GAITA',
            'province_id' => $idNorLipezPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VITICHI',
            'province_id' => $idNorLipezPotosi,




        ]);
        // NOR LIPEZ
        $idNorLipezPosoti = DB::table('provinces')->insertGetId([
            'name' => 'NOR LIPEZ',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLCHA K ',
            'province_id' => $idNorLipezPosoti,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO DE QUEMES',
            'province_id' => $idNorLipezPosoti,




        ]);
        // RAFAEL BUSTILLOS
        $idRafaelBustillosPotosi = DB::table('provinces')->insertGetId([
            'name' => 'RAFAEL BUSTILLOS',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'UNCIA',
            'province_id' => $idRafaelBustillosPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHAYANTA',
            'province_id' => $idRafaelBustillosPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'LLALLAGUA',
            'province_id' => $idRafaelBustillosPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHUQUIUTA',
            'province_id' => $idRafaelBustillosPotosi,




        ]);
        // sud chichas
        $idSudChichasPotosi = DB::table('provinces')->insertGetId([
            'name' => 'SUD CHICHAS',
            'department_id' => $idPotosi,
        ]);

        DB::table('municipalities')->insert([
            'name' => 'TUPIZA',
            'province_id' => $idSudChichasPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ATOCHA',
            'province_id' => $idSudChichasPotosi,




        ]);
        // sud lIpez
        $idSudLipezPosoti = DB::table('provinces')->insertGetId([
            'name' => 'SUD LIPEZ',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PABLO DE LIPEZ',
            'province_id' =>  $idSudLipezPosoti,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MOJINETE',
            'province_id' =>  $idSudLipezPosoti,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN ANTONIO DE ESMORUCO',
            'province_id' =>  $idSudLipezPosoti,




        ]);
        // tomas frias
        $idTomasFriasPotosi  = DB::table('provinces')->insertGetId([
            'name' => 'TOMAS FRIAS',
            'department_id' => $idPotosi,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'POTOSI',
            'province_id' => $idTomasFriasPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'TINGUIPAYA',
            'province_id' => $idTomasFriasPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'URMIRI',
            'province_id' => $idTomasFriasPotosi,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLA DE YOCALLA',
            'province_id' => $idTomasFriasPotosi,




        ]);


        //SANTA CRUZ ID - 8
        $idSantaCruz = DB::table('departments')->insertGetId([
            'name' => 'SANTA CRUZ',
            'abbreviation' => 'SRZ',
        ]);

        //ANDRES IBAÑEZ
        $idAndresIbañesSCZ = DB::table('provinces')->insertGetId([
            'name' => 'ANDRES IBAÑEZ',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTA CRUZ DE LA SIERRA',
            'province_id' => $idAndresIbañesSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COTOCA',
            'province_id' => $idAndresIbañesSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL TORNO',
            'province_id' => $idAndresIbañesSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'LA GUARDIA',
            'province_id' => $idAndresIbañesSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PORONGO',
            'province_id' => $idAndresIbañesSCZ,




        ]);
        //ANGEL SANDOVAL
        $idSandovalSCZ = DB::table('provinces')->insertGetId([
            'name' => 'ANGEL SANDOVAL',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN MATIAS',
            'province_id' => $idSandovalSCZ,




        ]);
        // CHIQUITOS
        $idChiquitosSCZ = DB::table('provinces')->insertGetId([
            'name' => 'CHIQUITOS',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN JOSE DE CHIQUITOS',
            'province_id' => $idChiquitosSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PAILON',
            'province_id' => $idChiquitosSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'ROBORE',
            'province_id' => $idChiquitosSCZ,




        ]);
        //CORDILLERA
        $idCordilleraSCZ = DB::table('provinces')->insertGetId([
            'name' => 'CORDILLERA',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'LAGUNILLAS',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BOYUIBE',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CABEZAS',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CAMIRI',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CHARAGUA',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CUEVO',
            'province_id' => $idCordilleraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'GUTIERREZ',
            'province_id' => $idCordilleraSCZ,




        ]);
        // FLORIDA
        $idFloridaSCZ = DB::table('provinces')->insertGetId([
            'name' => 'FLORIDA',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAMAIPATA',
            'province_id' => $idFloridaSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MAIRANA',
            'province_id' => $idFloridaSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PAMPA',
            'province_id' => $idFloridaSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'QUIRUSILLAS',
            'province_id' => $idFloridaSCZ,




        ]);
        // GERMAN BUSCH
        $idGermanBuschSCZ = DB::table('provinces')->insertGetId([
            'name' => 'GERMAN BUSCH',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO SUAREZ',
            'province_id' => $idGermanBuschSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL CARMEN RIVERO',
            'province_id' => $idGermanBuschSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUERTO QUIJARRO',
            'province_id' => $idGermanBuschSCZ,




        ]);
        // GUARAYOS
        $idGuarayosSCZ = DB::table('provinces')->insertGetId([
            'name' => 'GUARAYOS',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ASCENCION DE GUARAYOS',
            'province_id' => $idGuarayosSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL PUENTE',
            'province_id' => $idGuarayosSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'URUBICHA',
            'province_id' => $idGuarayosSCZ,




        ]);
        // ICHILO
        $idIchiloSCZ = DB::table('provinces')->insertGetId([
            'name' => 'ICHILO',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'BUENA VISTA',
            'province_id' => $idIchiloSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN CARLOS',
            'province_id' => $idIchiloSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN JUAN DE YAPACANI',
            'province_id' => $idIchiloSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'YAPACANI',
            'province_id' => $idIchiloSCZ,




        ]);

        // JOSE MIGUEL DE VELASCO
        $idJoseMigueldeVelascoSCZ = DB::table('provinces')->insertGetId([
            'name' => 'JOSE MIGUEL DE VELASCO',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN IGNACIO DE VELASCO',
            'province_id' => $idJoseMigueldeVelascoSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN MIGUEL DE VELASCO',
            'province_id' => $idJoseMigueldeVelascoSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN RAFAEL DE VELASCO',
            'province_id' => $idJoseMigueldeVelascoSCZ,




        ]);
        // MANUEL MARIA CABALLERO
        $idManuelMariaCaballeroSCZ = DB::table('provinces')->insertGetId([
            'name' => 'MANUEL MARIA CABALLERO',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'COMPARAPA',
            'province_id' => $idManuelMariaCaballeroSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAIPINA',
            'province_id' => $idManuelMariaCaballeroSCZ,




        ]);
        // ÑUFLO DE CHAVES
        $idNufloDeChavez = DB::table('provinces')->insertGetId([
            'name' => 'ÑUFLO DE CHAVEZ',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'CONCEPCION',
            'province_id' => $idNufloDeChavez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CUATRO CAÑADAS',
            'province_id' => $idNufloDeChavez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN ANTONIO DEL LOMERIO',
            'province_id' => $idNufloDeChavez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN JULIAN',
            'province_id' => $idNufloDeChavez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN RAMON',
            'province_id' => $idNufloDeChavez,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN XAVIER',
            'province_id' => $idNufloDeChavez,




        ]);
        // OBISPO SANTISTEVAN
        $idObispoSanistevanSCZ = DB::table('provinces')->insertGetId([
            'name' => 'OBISPO SANTISTEVAN',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'MONTERO',
            'province_id' => $idObispoSanistevanSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'GENERAL AGUSTIN SAAVEDRA',
            'province_id' => $idObispoSanistevanSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MINERO',
            'province_id' => $idObispoSanistevanSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'FERNANDEZ ALONSO',
            'province_id' => $idObispoSanistevanSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN PEDRO',
            'province_id' => $idObispoSanistevanSCZ,




        ]);

        // SARA
        $idSaraSCZ = DB::table('provinces')->insertGetId([
            'name' => 'SARA',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => '`PORTACHUELO',
            'province_id' => $idSaraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'COLPA BELGICA',
            'province_id' => $idSaraSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'SANTA ROSA DEL SARA',
            'province_id' => $idSaraSCZ,




        ]);
        // VALLEGRANDE
        $idVallegrandeSCZ = DB::table('provinces')->insertGetId([
            'name' => 'VALLEGRANDE',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'VALLEGRANDE',
            'province_id' => $idVallegrandeSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL TRIGAL',
            'province_id' => $idVallegrandeSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'MORO MORO',
            'province_id' => $idVallegrandeSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'POSTREVALLE',
            'province_id' => $idVallegrandeSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'PUCARA',
            'province_id' => $idVallegrandeSCZ,




        ]);
        //WARNES
        $idWarnesSCZ = DB::table('provinces')->insertGetId([
            'name' => 'WARNES',
            'department_id' => $idSantaCruz,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'WARNES',
            'province_id' => $idWarnesSCZ,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'OKINAWA',
            'province_id' => $idWarnesSCZ,




        ]);

        //TARIJA - ID 9
        $idTarija = DB::table('departments')->insertGetId([
            'name' => 'TARIJA',
            'abbreviation' => 'TJA',
        ]);
        // ANICETO ARCE
        $idAnicetoArceTarija = DB::table('provinces')->insertGetId([
            'name' => 'ANICETO ARCE',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'PADCAYA',
            'province_id' => $idAnicetoArceTarija,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'BERMEJO',
            'province_id' => $idAnicetoArceTarija,




        ]);
        // AVIELS
        $idAvilesTarija = DB::table('provinces')->insertGetId([
            'name' => 'AVILES',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'URIONDO',
            'province_id' => $idAvilesTarija,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'YUNCHARA',
            'province_id' => $idAvilesTarija,




        ]);
        // CERCADO
        $idCercadoTarija = DB::table('provinces')->insertGetId([
            'name' => 'CERCADO',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'TARIJA',
            'province_id' => $idCercadoTarija,




        ]);
        // GRAN CHACO
        $idGranChacoTarija = DB::table('provinces')->insertGetId([
            'name' => 'GRAN CHACO',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'YACUIBA',
            'province_id' => $idGranChacoTarija,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'CARAPARI',
            'province_id' => $idGranChacoTarija,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'VILLAMONTES',
            'province_id' => $idGranChacoTarija,




        ]);
        // MENDEZ
        $idMendezTarija = DB::table('provinces')->insertGetId([
            'name' => 'MENDEZ',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'SAN LORENZO',
            'province_id' => $idMendezTarija,




        ]);
        DB::table('municipalities')->insert([
            'name' => 'EL PUENTE',
            'province_id' => $idMendezTarija,




        ]);
        //O-CONNOR

        $idOconnorTarija = DB::table('provinces')->insertGetId([
            'name' => 'O-CONNOR',
            'department_id' => $idTarija,
        ]);
        DB::table('municipalities')->insert([
            'name' => 'ENTRE RIOS',
            'province_id' => $idOconnorTarija,
        ]);


        
    }
}
