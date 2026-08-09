<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StationsCompleteSeeder extends Seeder
{
    /**
     * Genera un UUID (Universally Unique Identifier) para la estación
     * Formato: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx (Ej: 550e8400-e29b-41d4-a716-446655440000)
     */
    private function generateUniqueStationCode()
    {
        return (string) Str::uuid();
    }
    
    /**
     * Run the database seeds.
     * 
     * Este seeder crea las estaciones definidas en $locations con sus datos principales:
     * - Station (estación principal)
     * - DetailStation (detalles de la estación)
     * - WeatherStation (estación meteorológica)
     */
    public function run(): void
    {
        // Datos de ubicaciones en Bolivia
        $locations = [
            ['name' => 'Santa Cruz Centro', 'location' => 'Santa Cruz de la Sierra, Andrés Ibáñez, SC', 'lat' => '-17.783333', 'lon' => '-63.182222', 'elevation' => 416],
            ['name' => 'La Paz Plaza Murillo', 'location' => 'La Paz, Pedro Domingo Murillo, LP', 'lat' => '-16.500000', 'lon' => '-68.150000', 'elevation' => 3640],
            ['name' => 'Cochabamba Centro', 'location' => 'Cochabamba, Cercado, CB', 'lat' => '-17.393333', 'lon' => '-66.157222', 'elevation' => 2558],
            ['name' => 'Tarija San Lorenzo', 'location' => 'San Lorenzo, Eustaquio Méndez, TJ', 'lat' => '-21.412778', 'lon' => '-64.745556', 'elevation' => 1875],
            ['name' => 'Sucre Histórico', 'location' => 'Sucre, Oropeza, CH', 'lat' => '-19.033333', 'lon' => '-65.266667', 'elevation' => 2810],
            ['name' => 'Potosí Minero', 'location' => 'Potosí, Tomás Frías, PT', 'lat' => '-19.583333', 'lon' => '-65.750000', 'elevation' => 4090],
            ['name' => 'Oruro Altiplano', 'location' => 'Oruro, Cercado, OR', 'lat' => '-17.966667', 'lon' => '-67.116667', 'elevation' => 3706],
            ['name' => 'Beni Trinidad', 'location' => 'Trinidad, Cercado, BN', 'lat' => '-14.833333', 'lon' => '-64.900000', 'elevation' => 155],
            ['name' => 'Pando Cobija', 'location' => 'Cobija, Nicolás Suárez, PD', 'lat' => '-11.027778', 'lon' => '-68.769444', 'elevation' => 280],
            ['name' => 'El Alto Aeropuerto', 'location' => 'El Alto, Pedro Domingo Murillo, LP', 'lat' => '-16.505556', 'lon' => '-68.192222', 'elevation' => 4071],
            ['name' => 'Montero Agrícola', 'location' => 'Montero, Obispo Santistevan, SC', 'lat' => '-17.337222', 'lon' => '-63.250556', 'elevation' => 300],
            ['name' => 'Warnes Industrial', 'location' => 'Warnes, Warnes, SC', 'lat' => '-17.516667', 'lon' => '-63.166667', 'elevation' => 375],
            ['name' => 'Quillacollo Valle', 'location' => 'Quillacollo, Quillacollo, CB', 'lat' => '-17.393056', 'lon' => '-66.279167', 'elevation' => 2548],
            ['name' => 'Tiquipaya Rural', 'location' => 'Tiquipaya, Quillacollo, CB', 'lat' => '-17.337500', 'lon' => '-66.216667', 'elevation' => 2750],
            ['name' => 'Sacaba Urbano', 'location' => 'Sacaba, Chapare, CB', 'lat' => '-17.398056', 'lon' => '-66.038333', 'elevation' => 2650],
            ['name' => 'Yacuiba Frontera', 'location' => 'Yacuiba, Gran Chaco, TJ', 'lat' => '-22.016667', 'lon' => '-63.683333', 'elevation' => 650],
            ['name' => 'Villamontes Chaco', 'location' => 'Villamontes, Gran Chaco, TJ', 'lat' => '-21.266667', 'lon' => '-63.466667', 'elevation' => 400],
            ['name' => 'Uyuni Salar', 'location' => 'Uyuni, Antonio Quijarro, PT', 'lat' => '-20.461111', 'lon' => '-66.825000', 'elevation' => 3665],
            ['name' => 'Tupiza Histórico', 'location' => 'Tupiza, Sud Chichas, PT', 'lat' => '-21.450000', 'lon' => '-65.716667', 'elevation' => 2950],
            ['name' => 'Camiri Petrolero', 'location' => 'Camiri, Cordillera, SC', 'lat' => '-20.047222', 'lon' => '-63.520833', 'elevation' => 820],
            ['name' => 'Riberalta Amazónico', 'location' => 'Riberalta, Vaca Díez, BN', 'lat' => '-11.008056', 'lon' => '-66.063889', 'elevation' => 141],
            ['name' => 'Guayaramerín Puerto', 'location' => 'Guayaramerín, Vaca Díez, BN', 'lat' => '-10.821667', 'lon' => '-65.358333', 'elevation' => 130],
            ['name' => 'Rurrenabaque Turístico', 'location' => 'Rurrenabaque, Beni, BN', 'lat' => '-14.438333', 'lon' => '-67.528889', 'elevation' => 205],
            ['name' => 'Copacabana Lago', 'location' => 'Copacabana, Manco Kapac, LP', 'lat' => '-16.166111', 'lon' => '-69.088611', 'elevation' => 3841],
            ['name' => 'Sorata Montaña', 'location' => 'Sorata, Larecaja, LP', 'lat' => '-15.771944', 'lon' => '-68.650000', 'elevation' => 2695],
            ['name' => 'Coroico Yungas', 'location' => 'Coroico, Nor Yungas, LP', 'lat' => '-16.188889', 'lon' => '-67.725000', 'elevation' => 1750],
            ['name' => 'Samaipata Valle', 'location' => 'Samaipata, Florida, SC', 'lat' => '-18.179444', 'lon' => '-63.872778', 'elevation' => 1650],
            ['name' => 'Vallegrande Histórico', 'location' => 'Vallegrande, Vallegrande, SC', 'lat' => '-18.489167', 'lon' => '-64.109722', 'elevation' => 2030],
            ['name' => 'Chulumani Yungas', 'location' => 'Chulumani, Sud Yungas, LP', 'lat' => '-16.408333', 'lon' => '-67.526667', 'elevation' => 1750],
            ['name' => 'Ascención de Guarayos', 'location' => 'Ascensión, Guarayos, SC', 'lat' => '-15.721389', 'lon' => '-63.143611', 'elevation' => 250],

            // ---- La Paz (LP) ----
            ['name' => 'Achacachi Omasuyos', 'location' => 'Achacachi, Omasuyos, LP', 'lat' => '-16.050000', 'lon' => '-68.683333', 'elevation' => 3854],
            ['name' => 'Viacha Ingavi', 'location' => 'Viacha, Ingavi, LP', 'lat' => '-16.654722', 'lon' => '-68.296944', 'elevation' => 3850],
            ['name' => 'Caranavi Tropical', 'location' => 'Caranavi, Caranavi, LP', 'lat' => '-15.833333', 'lon' => '-67.566667', 'elevation' => 600],
            ['name' => 'Patacamaya Aroma', 'location' => 'Patacamaya, Aroma, LP', 'lat' => '-17.236111', 'lon' => '-67.921389', 'elevation' => 3789],
            ['name' => 'Guanay Larecaja', 'location' => 'Guanay, Larecaja, LP', 'lat' => '-15.500000', 'lon' => '-67.883333', 'elevation' => 520],
            ['name' => 'Apolo Franz Tamayo', 'location' => 'Apolo, Franz Tamayo, LP', 'lat' => '-14.716667', 'lon' => '-68.416667', 'elevation' => 1415],
            ['name' => 'Coripata Yungas', 'location' => 'Coripata, Nor Yungas, LP', 'lat' => '-16.319444', 'lon' => '-67.616667', 'elevation' => 1500],
            ['name' => 'Tiwanaku Arqueológico', 'location' => 'Tiwanaku, Ingavi, LP', 'lat' => '-16.554167', 'lon' => '-68.680556', 'elevation' => 3870],
            ['name' => 'Desaguadero Frontera', 'location' => 'Desaguadero, Ingavi, LP', 'lat' => '-16.566667', 'lon' => '-69.041667', 'elevation' => 3812],
            ['name' => 'Sica Sica Aroma', 'location' => 'Sica Sica, Aroma, LP', 'lat' => '-17.330556', 'lon' => '-67.738889', 'elevation' => 3920],
            ['name' => 'Pucarani Los Andes', 'location' => 'Pucarani, Los Andes, LP', 'lat' => '-16.433333', 'lon' => '-68.500000', 'elevation' => 3860],
            ['name' => 'Achocalla Murillo', 'location' => 'Achocalla, Murillo, LP', 'lat' => '-16.566667', 'lon' => '-68.166667', 'elevation' => 3800],
            ['name' => 'Palos Blancos Sur', 'location' => 'Palos Blancos, Sud Yungas, LP', 'lat' => '-15.616667', 'lon' => '-67.183333', 'elevation' => 420],
            ['name' => 'Quime Inquisivi', 'location' => 'Quime, Inquisivi, LP', 'lat' => '-16.991667', 'lon' => '-67.216667', 'elevation' => 2970],
            ['name' => 'Irupana Sud Yungas', 'location' => 'Irupana, Sud Yungas, LP', 'lat' => '-16.483333', 'lon' => '-67.461111', 'elevation' => 1900],
            ['name' => 'Charazani Cordillera', 'location' => 'Charazani, Bautista Saavedra, LP', 'lat' => '-15.183333', 'lon' => '-69.016667', 'elevation' => 3200],
            ['name' => 'Mecapaca Valle', 'location' => 'Mecapaca, Murillo, LP', 'lat' => '-16.683333', 'lon' => '-68.016667', 'elevation' => 3000],
            ['name' => 'Batallas Los Andes', 'location' => 'Batallas, Los Andes, LP', 'lat' => '-16.300000', 'lon' => '-68.533333', 'elevation' => 3850],

            // ---- Santa Cruz (SC) ----
            ['name' => 'La Guardia Urbano', 'location' => 'La Guardia, Andrés Ibáñez, SC', 'lat' => '-17.900000', 'lon' => '-63.320000', 'elevation' => 450],
            ['name' => 'Cotoca Religioso', 'location' => 'Cotoca, Andrés Ibáñez, SC', 'lat' => '-17.750000', 'lon' => '-63.055556', 'elevation' => 380],
            ['name' => 'Portachuelo Sara', 'location' => 'Portachuelo, Sara, SC', 'lat' => '-17.350000', 'lon' => '-63.400000', 'elevation' => 320],
            ['name' => 'Buena Vista Ichilo', 'location' => 'Buena Vista, Ichilo, SC', 'lat' => '-17.450000', 'lon' => '-63.661111', 'elevation' => 380],
            ['name' => 'San Ignacio de Velasco', 'location' => 'San Ignacio, José Miguel de Velasco, SC', 'lat' => '-16.366667', 'lon' => '-60.950000', 'elevation' => 413],
            ['name' => 'San José de Chiquitos', 'location' => 'San José, Chiquitos, SC', 'lat' => '-17.838889', 'lon' => '-60.741667', 'elevation' => 320],
            ['name' => 'Roboré Chiquitania', 'location' => 'Roboré, Chiquitos, SC', 'lat' => '-18.333333', 'lon' => '-59.762500', 'elevation' => 280],
            ['name' => 'Puerto Suárez Pantanal', 'location' => 'Puerto Suárez, Germán Busch, SC', 'lat' => '-18.950000', 'lon' => '-57.800000', 'elevation' => 134],
            ['name' => 'Puerto Quijarro Frontera', 'location' => 'Puerto Quijarro, Germán Busch, SC', 'lat' => '-17.783333', 'lon' => '-57.770000', 'elevation' => 120],
            ['name' => 'Concepción Misional', 'location' => 'Concepción, Ñuflo de Chávez, SC', 'lat' => '-16.133333', 'lon' => '-62.020000', 'elevation' => 490],
            ['name' => 'San Javier Misional', 'location' => 'San Javier, Ñuflo de Chávez, SC', 'lat' => '-16.272222', 'lon' => '-62.500000', 'elevation' => 550],
            ['name' => 'Mairana Florida', 'location' => 'Mairana, Florida, SC', 'lat' => '-18.120000', 'lon' => '-63.950000', 'elevation' => 1320],
            ['name' => 'Comarapa Caballero', 'location' => 'Comarapa, Manuel María Caballero, SC', 'lat' => '-17.910000', 'lon' => '-64.530000', 'elevation' => 1800],
            ['name' => 'Mineros Agroindustrial', 'location' => 'Mineros, Obispo Santistevan, SC', 'lat' => '-17.113889', 'lon' => '-63.230556', 'elevation' => 270],
            ['name' => 'San Julián Colonizador', 'location' => 'San Julián, Ñuflo de Chávez, SC', 'lat' => '-16.850000', 'lon' => '-62.660000', 'elevation' => 300],
            ['name' => 'Yapacaní Ichilo', 'location' => 'Yapacaní, Ichilo, SC', 'lat' => '-17.400000', 'lon' => '-63.850000', 'elevation' => 320],
            ['name' => 'El Torno Valle', 'location' => 'El Torno, Andrés Ibáñez, SC', 'lat' => '-17.972222', 'lon' => '-63.391667', 'elevation' => 480],
            ['name' => 'Charagua Chaco', 'location' => 'Charagua, Cordillera, SC', 'lat' => '-19.794444', 'lon' => '-63.200000', 'elevation' => 760],
            ['name' => 'Okinawa Uno', 'location' => 'Okinawa Uno, Warnes, SC', 'lat' => '-17.230000', 'lon' => '-62.880000', 'elevation' => 280],
            ['name' => 'San Matías Pantanal', 'location' => 'San Matías, Ángel Sandoval, SC', 'lat' => '-16.363889', 'lon' => '-58.401389', 'elevation' => 130],

            // ---- Cochabamba (CB) ----
            ['name' => 'Punata Valle Alto', 'location' => 'Punata, Punata, CB', 'lat' => '-17.541667', 'lon' => '-65.831944', 'elevation' => 2730],
            ['name' => 'Cliza Valle Alto', 'location' => 'Cliza, Germán Jordán, CB', 'lat' => '-17.591667', 'lon' => '-65.930556', 'elevation' => 2720],
            ['name' => 'Aiquile Campero', 'location' => 'Aiquile, Campero, CB', 'lat' => '-18.200000', 'lon' => '-65.183333', 'elevation' => 2250],
            ['name' => 'Villa Tunari Trópico', 'location' => 'Villa Tunari, Chapare, CB', 'lat' => '-16.970000', 'lon' => '-65.420000', 'elevation' => 300],
            ['name' => 'Shinahota Trópico', 'location' => 'Shinahota, Tiraque, CB', 'lat' => '-17.050000', 'lon' => '-65.300000', 'elevation' => 290],
            ['name' => 'Colomi Chapare', 'location' => 'Colomi, Chapare, CB', 'lat' => '-17.350000', 'lon' => '-65.870000', 'elevation' => 3250],
            ['name' => 'Tarata Esteban Arce', 'location' => 'Tarata, Esteban Arce, CB', 'lat' => '-17.611111', 'lon' => '-66.016667', 'elevation' => 2750],
            ['name' => 'Capinota Valle Bajo', 'location' => 'Capinota, Capinota, CB', 'lat' => '-17.713889', 'lon' => '-66.263889', 'elevation' => 2390],
            ['name' => 'Tiraque Cordillera', 'location' => 'Tiraque, Tiraque, CB', 'lat' => '-17.420000', 'lon' => '-65.720000', 'elevation' => 3300],
            ['name' => 'Mizque Valle', 'location' => 'Mizque, Mizque, CB', 'lat' => '-17.940000', 'lon' => '-65.341667', 'elevation' => 2050],
            ['name' => 'Sipe Sipe Quillacollo', 'location' => 'Sipe Sipe, Quillacollo, CB', 'lat' => '-17.450000', 'lon' => '-66.383333', 'elevation' => 2550],
            ['name' => 'Vinto Quillacollo', 'location' => 'Vinto, Quillacollo, CB', 'lat' => '-17.400000', 'lon' => '-66.313889', 'elevation' => 2560],
            ['name' => 'Arani Valle Alto', 'location' => 'Arani, Arani, CB', 'lat' => '-17.570000', 'lon' => '-65.770000', 'elevation' => 2740],
            ['name' => 'Independencia Ayopaya', 'location' => 'Independencia, Ayopaya, CB', 'lat' => '-17.090000', 'lon' => '-66.810000', 'elevation' => 2840],
            ['name' => 'Morochata Ayopaya', 'location' => 'Morochata, Ayopaya, CB', 'lat' => '-17.200000', 'lon' => '-66.420000', 'elevation' => 3500],
            ['name' => 'Tapacarí Cordillera', 'location' => 'Tapacarí, Tapacarí, CB', 'lat' => '-17.600000', 'lon' => '-66.700000', 'elevation' => 3300],

            // ---- Chuquisaca (CH) ----
            ['name' => 'Camargo Cinti', 'location' => 'Camargo, Nor Cinti, CH', 'lat' => '-20.638889', 'lon' => '-65.211111', 'elevation' => 2410],
            ['name' => 'Monteagudo Chaco', 'location' => 'Monteagudo, Hernando Siles, CH', 'lat' => '-19.800000', 'lon' => '-63.958333', 'elevation' => 1130],
            ['name' => 'Villa Serrano Boeto', 'location' => 'Villa Serrano, Belisario Boeto, CH', 'lat' => '-19.111111', 'lon' => '-64.319444', 'elevation' => 2110],
            ['name' => 'Padilla Tomina', 'location' => 'Padilla, Tomina, CH', 'lat' => '-19.300000', 'lon' => '-64.300000', 'elevation' => 2100],
            ['name' => 'Tarabuco Textil', 'location' => 'Tarabuco, Yamparáez, CH', 'lat' => '-19.183333', 'lon' => '-64.916667', 'elevation' => 3280],
            ['name' => 'Zudáñez Valle', 'location' => 'Zudáñez, Zudáñez, CH', 'lat' => '-19.100000', 'lon' => '-64.716667', 'elevation' => 2480],
            ['name' => 'Villa Abecia Vitivinícola', 'location' => 'Villa Abecia, Sud Cinti, CH', 'lat' => '-20.961111', 'lon' => '-65.300000', 'elevation' => 2350],
            ['name' => 'Culpina Nor Cinti', 'location' => 'Culpina, Nor Cinti, CH', 'lat' => '-20.833333', 'lon' => '-64.950000', 'elevation' => 3000],

            // ---- Tarija (TJ) ----
            ['name' => 'Bermejo Frontera', 'location' => 'Bermejo, Arce, TJ', 'lat' => '-22.733333', 'lon' => '-64.341667', 'elevation' => 415],
            ['name' => 'Entre Ríos O\'Connor', 'location' => 'Entre Ríos, O\'Connor, TJ', 'lat' => '-21.516667', 'lon' => '-64.166667', 'elevation' => 1200],
            ['name' => 'Padcaya Arce', 'location' => 'Padcaya, Arce, TJ', 'lat' => '-21.870000', 'lon' => '-64.710000', 'elevation' => 1980],
            ['name' => 'Uriondo Vitivinícola', 'location' => 'Uriondo, Avilés, TJ', 'lat' => '-21.690000', 'lon' => '-64.690000', 'elevation' => 1850],
            ['name' => 'El Puente Méndez', 'location' => 'El Puente, Méndez, TJ', 'lat' => '-21.230000', 'lon' => '-65.220000', 'elevation' => 2330],
            ['name' => 'Caraparí Gran Chaco', 'location' => 'Caraparí, Gran Chaco, TJ', 'lat' => '-21.810000', 'lon' => '-63.740000', 'elevation' => 800],

            // ---- Potosí (PT) ----
            ['name' => 'Villazón Frontera', 'location' => 'Villazón, Modesto Omiste, PT', 'lat' => '-22.086111', 'lon' => '-65.593056', 'elevation' => 3443],
            ['name' => 'Llallagua Minero', 'location' => 'Llallagua, Rafael Bustillo, PT', 'lat' => '-18.421389', 'lon' => '-66.586111', 'elevation' => 3850],
            ['name' => 'Uncía Bustillo', 'location' => 'Uncía, Rafael Bustillo, PT', 'lat' => '-18.461111', 'lon' => '-66.572222', 'elevation' => 3750],
            ['name' => 'Atocha Sud Chichas', 'location' => 'Atocha, Sud Chichas, PT', 'lat' => '-20.930000', 'lon' => '-66.230000', 'elevation' => 3700],
            ['name' => 'Colquechaca Chayanta', 'location' => 'Colquechaca, Chayanta, PT', 'lat' => '-18.680000', 'lon' => '-66.020000', 'elevation' => 4050],
            ['name' => 'Cotagaita Nor Chichas', 'location' => 'Cotagaita, Nor Chichas, PT', 'lat' => '-20.820000', 'lon' => '-65.690000', 'elevation' => 2700],
            ['name' => 'Tinguipaya Tomás Frías', 'location' => 'Tinguipaya, Tomás Frías, PT', 'lat' => '-19.200000', 'lon' => '-65.850000', 'elevation' => 3600],
            ['name' => 'Betanzos Saavedra', 'location' => 'Betanzos, Cornelio Saavedra, PT', 'lat' => '-19.550000', 'lon' => '-65.450000', 'elevation' => 3150],
            ['name' => 'Caiza D Linares', 'location' => 'Caiza D, José María Linares, PT', 'lat' => '-20.030000', 'lon' => '-65.650000', 'elevation' => 3300],
            ['name' => 'Puna Linares', 'location' => 'Puna, José María Linares, PT', 'lat' => '-19.780000', 'lon' => '-65.500000', 'elevation' => 3420],

            // ---- Oruro (OR) ----
            ['name' => 'Huanuni Minero', 'location' => 'Huanuni, Pantaleón Dalence, OR', 'lat' => '-18.270000', 'lon' => '-66.840000', 'elevation' => 3990],
            ['name' => 'Challapata Avaroa', 'location' => 'Challapata, Eduardo Avaroa, OR', 'lat' => '-18.900000', 'lon' => '-66.770000', 'elevation' => 3740],
            ['name' => 'Caracollo Cercado', 'location' => 'Caracollo, Cercado, OR', 'lat' => '-17.630000', 'lon' => '-67.200000', 'elevation' => 3780],
            ['name' => 'Poopó Lago', 'location' => 'Poopó, Poopó, OR', 'lat' => '-18.380000', 'lon' => '-66.970000', 'elevation' => 3760],
            ['name' => 'Machacamarca Dalence', 'location' => 'Machacamarca, Pantaleón Dalence, OR', 'lat' => '-18.160000', 'lon' => '-67.050000', 'elevation' => 3710],
            ['name' => 'Totora Altiplano', 'location' => 'Totora, San Pedro de Totora, OR', 'lat' => '-17.720000', 'lon' => '-68.180000', 'elevation' => 3870],
            ['name' => 'Toledo Saucarí', 'location' => 'Toledo, Saucarí, OR', 'lat' => '-18.000000', 'lon' => '-67.280000', 'elevation' => 3720],
            ['name' => 'Corque Carangas', 'location' => 'Corque, Carangas, OR', 'lat' => '-18.340000', 'lon' => '-67.690000', 'elevation' => 3870],

            // ---- Beni (BN) ----
            ['name' => 'San Borja Ballivián', 'location' => 'San Borja, José Ballivián, BN', 'lat' => '-14.858333', 'lon' => '-66.850000', 'elevation' => 194],
            ['name' => 'San Ignacio de Moxos', 'location' => 'San Ignacio, Moxos, BN', 'lat' => '-14.970000', 'lon' => '-65.633333', 'elevation' => 160],
            ['name' => 'Santa Ana del Yacuma', 'location' => 'Santa Ana, Yacuma, BN', 'lat' => '-13.741667', 'lon' => '-65.430000', 'elevation' => 144],
            ['name' => 'Magdalena Iténez', 'location' => 'Magdalena, Iténez, BN', 'lat' => '-13.260000', 'lon' => '-64.060000', 'elevation' => 140],
            ['name' => 'San Ramón Mamoré', 'location' => 'San Ramón, Mamoré, BN', 'lat' => '-13.270000', 'lon' => '-64.710000', 'elevation' => 150],
            ['name' => 'Reyes Ballivián', 'location' => 'Reyes, José Ballivián, BN', 'lat' => '-14.280000', 'lon' => '-67.340000', 'elevation' => 230],
            ['name' => 'Loreto Marbán', 'location' => 'Loreto, Marbán, BN', 'lat' => '-15.220000', 'lon' => '-64.780000', 'elevation' => 180],
            ['name' => 'Baures Iténez', 'location' => 'Baures, Iténez, BN', 'lat' => '-13.580000', 'lon' => '-63.580000', 'elevation' => 130],
            ['name' => 'Exaltación Yacuma', 'location' => 'Exaltación, Yacuma, BN', 'lat' => '-13.270000', 'lon' => '-65.360000', 'elevation' => 130],

            // ---- Pando (PD) ----
            ['name' => 'Porvenir Nicolás Suárez', 'location' => 'Porvenir, Nicolás Suárez, PD', 'lat' => '-11.250000', 'lon' => '-68.660000', 'elevation' => 250],
            ['name' => 'Puerto Rico Manuripi', 'location' => 'Puerto Rico, Manuripi, PD', 'lat' => '-11.100000', 'lon' => '-67.550000', 'elevation' => 200],
            ['name' => 'Filadelfia Manuripi', 'location' => 'Filadelfia, Manuripi, PD', 'lat' => '-11.420000', 'lon' => '-68.650000', 'elevation' => 240],
            ['name' => 'Bella Flor Pando', 'location' => 'Bella Flor, Nicolás Suárez, PD', 'lat' => '-11.550000', 'lon' => '-68.500000', 'elevation' => 230],
            ['name' => 'El Sena Madre de Dios', 'location' => 'El Sena, Madre de Dios, PD', 'lat' => '-11.500000', 'lon' => '-67.300000', 'elevation' => 190],
            // ---- Estaciones caidas para pruebas de monitoreo ----
            ['name' => 'Caida Dos Semanas 01', 'location' => 'El Puente, Cercado, OR', 'lat' => '-17.940000', 'lon' => '-67.070000', 'elevation' => 3710],
            ['name' => 'Caida Dos Semanas 02', 'location' => 'Paria, Cercado, OR', 'lat' => '-17.850000', 'lon' => '-67.000000', 'elevation' => 3730],
            ['name' => 'Caida Dos Semanas 03', 'location' => 'Laja, Los Andes, LP', 'lat' => '-16.530000', 'lon' => '-68.380000', 'elevation' => 3865],
            ['name' => 'Caida Dos Semanas 04', 'location' => 'Tiahuanacu, Ingavi, LP', 'lat' => '-16.560000', 'lon' => '-68.680000', 'elevation' => 3872],
            ['name' => 'Caida Dos Semanas 05', 'location' => 'San Pedro, Aroma, LP', 'lat' => '-17.270000', 'lon' => '-67.930000', 'elevation' => 3795],
            ['name' => 'Caida Dos Semanas 06', 'location' => 'Ivirgarzama, Carrasco, CB', 'lat' => '-17.030000', 'lon' => '-64.850000', 'elevation' => 245],
            ['name' => 'Caida Dos Semanas 07', 'location' => 'Chimore, Carrasco, CB', 'lat' => '-16.980000', 'lon' => '-65.130000', 'elevation' => 260],
            ['name' => 'Caida Dos Semanas 08', 'location' => 'San Carlos, Ichilo, SC', 'lat' => '-17.400000', 'lon' => '-63.730000', 'elevation' => 300],
            ['name' => 'Caida Dos Semanas 09', 'location' => 'Colpa Belgica, Sara, SC', 'lat' => '-17.550000', 'lon' => '-63.260000', 'elevation' => 330],
            ['name' => 'Caida Dos Semanas 10', 'location' => 'San Pedro, Obispo Santistevan, SC', 'lat' => '-16.830000', 'lon' => '-63.500000', 'elevation' => 285],
            ['name' => 'Caida Mas Dos Semanas 01', 'location' => 'San Lorenzo, Cercado, BN', 'lat' => '-14.730000', 'lon' => '-64.910000', 'elevation' => 160],
            ['name' => 'Caida Mas Dos Semanas 02', 'location' => 'San Andres, Marban, BN', 'lat' => '-15.080000', 'lon' => '-64.820000', 'elevation' => 180],
            ['name' => 'Caida Mas Dos Semanas 03', 'location' => 'Puerto Siles, Mamore, BN', 'lat' => '-12.950000', 'lon' => '-64.750000', 'elevation' => 145],
            ['name' => 'Caida Mas Dos Semanas 04', 'location' => 'Santos Mercado, Federico Roman, PD', 'lat' => '-11.020000', 'lon' => '-66.080000', 'elevation' => 160],
            ['name' => 'Caida Mas Dos Semanas 05', 'location' => 'Nueva Esperanza, Federico Roman, PD', 'lat' => '-10.920000', 'lon' => '-66.220000', 'elevation' => 170],
            ['name' => 'Caida Mas Dos Semanas 06', 'location' => 'San Agustin, Enrique Baldivieso, PT', 'lat' => '-21.150000', 'lon' => '-67.680000', 'elevation' => 3950],
            ['name' => 'Caida Mas Dos Semanas 07', 'location' => 'Porco, Antonio Quijarro, PT', 'lat' => '-19.800000', 'lon' => '-65.980000', 'elevation' => 4100],
            ['name' => 'Caida Mas Dos Semanas 08', 'location' => 'Yunchara, Aviles, TJ', 'lat' => '-21.750000', 'lon' => '-65.180000', 'elevation' => 3350],
            ['name' => 'Caida Mas Dos Semanas 09', 'location' => 'Iscayachi, Mendez, TJ', 'lat' => '-21.430000', 'lon' => '-65.130000', 'elevation' => 3420],
            ['name' => 'Caida Mas Dos Semanas 10', 'location' => 'Tomina, Tomina, CH', 'lat' => '-19.180000', 'lon' => '-64.480000', 'elevation' => 2200],
        ];

        $brands = ['Davis', 'Campbell Scientific'];
        $models = [
            'Davis' => ['Vantage Pro 2 Plus', 'Vantage Pro 2', 'Vantage Vue', 'Vantage Pro Weather'],
            'Campbell Scientific' => ['CR1000', 'CR3000', 'CR6']
        ];
        $powerSupply = ['Photovoltaic', 'Electric'];

        // Crear todas las estaciones definidas en $locations
        $total = count($locations);
        for ($i = 1; $i <= $total; $i++) {
            $location = $locations[$i - 1];
            $brand = $brands[array_rand($brands)];
            $model = $models[$brand][array_rand($models[$brand])];
            
            // Buscar si ya existe la estación por nombre (o podrías usar un código fijo si lo tuvieras)
            $existingStation = DB::table('stations')->where('name', $location['name'])->first();
            
            if ($existingStation) {
                $stationId = $existingStation->id;
                DB::table('stations')->where('id', $stationId)->update([
                    'location' => $location['location'],
                    'address' => 'Calle Principal #' . rand(100, 999),
                    'search_concatenation' => '┆' . $location['name'] . '┆' . $location['location'] . '┆Calle Principal #' . rand(100, 999),
                    'latitude' => $location['lat'],
                    'longitude' => $location['lon'],
                    'updated_at' => now(),
                ]);
            } else {
                $stationId = DB::table('stations')->insertGetId([
                    'code' => $this->generateUniqueStationCode(),
                    'name' => $location['name'],
                    'location' => $location['location'],
                    'address' => 'Calle Principal #' . rand(100, 999),
                    'search_concatenation' => '┆' . $location['name'] . '┆' . $location['location'] . '┆Calle Principal #' . rand(100, 999),
                    'reg_date' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d'),
                    'latitude' => $location['lat'],
                    'longitude' => $location['lon'],
                    'state' => 1,
                    'et' => rand(0, 1),
                    'user_creator_id' => null,
                    'modifier_user_id' => null,
                    'municipality_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 1. Detail Station
            DB::table('details_stations')->updateOrInsert(
                ['station_id' => $stationId],
                [
                    'brand' => $brand,
                    'power_supply' => $powerSupply[array_rand($powerSupply)],
                    'reception_type' => rand(0, 1) ? 'WiFi' : 'Ethernet',
                    'model' => $model,
                    'installation_date' => Carbon::now()->subDays(rand(30, 730))->format('Y-m-d'),
                    'elevation' => $location['elevation'],
                    'state' => 1,
                    'updated_at' => now(),
                ]
            );

            echo "Estación {$i}/{$total} creada/actualizada: {$location['name']}\n";
        }

        echo "\n✅ Seeder completado: {$total} estaciones con sus datos principales.\n";
    }
}
