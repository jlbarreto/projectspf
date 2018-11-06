<?php

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();

        // $this->call('UserTableSeeder');
        $this->call('res_service_typesTableSeeder');
        $this->command->info('Tipos de servicios creados exitosamente');

        $this->call('res_restaurantsTableSeeder');
        $this->command->info('Restaurantes de prueba creados exitosamente');

        $this->call('res_SectionTableSeeder');
        $this->command->info('Secciones de restaurantes de prueba creados exitosamente');

        $this->call('res_ProductTableSeeder');
        $this->command->info('Productos creados exitosamente');

        $this->call('res_payment_methodsTableSeeder');
        $this->command->info('Tipos de pago creados exitosamente');
    }

}

class res_service_typesTableSeeder extends Seeder {

    function run() {
        DB::table('res_service_types')->insert(array(
            'service_type' => 'Take out',
            'created_at' => new DateTime
        ));

        DB::table('res_service_types')->insert(array(
            'service_type' => 'Delivery',
            'created_at' => new DateTime
        ));
    }

}

class res_restaurantsTableSeeder extends Seeder {

    function run() {
        DB::table('res_restaurants')->insert(array(
            'name' => 'Pizza Hut',
            'orders_allocator_id' => '1',
            'parent_restaurant_id' => '1',
            'delivery_time' => '1',
            'guarantee_time' => '1',
            'shipping_cost' => '0.50',
            'minimum_order' => '5.00',
            'phone' => '2257-7777',
            'address' => 'Todo El Salvador',
            'map_coordinates' => '-15.98',
            'search_reserved_position' => '1',
            'days_as_new' => '1',
            'slug' => 'pizza-hut',
            'created_at' => new DateTime
        ));
    }

}

class res_SectionTableSeeder extends Seeder {

    function run() {
        DB::table('res_sections')->insert(array(
            'restaurant_id' => '1',
            'section' => 'pizzas',
            'section_order_id' => '1',
            'created_at' => new DateTime
        ));
    }

}

class res_ProductTableSeeder extends Seeder {

    function run() {
        DB::table('res_products')->insert(array(
            'product' => 'pizza de hongos',
            'description' => 'pizza de hongos',
            'value' => '10.90',
            'section_id' => '1',
            'slug' => 'pizza-de-hongos',
            'activate' => '1',
            'created_at' => new DateTime
        ));
    }

}

class res_payment_methodsTableSeeder extends Seeder {

    function run() {
        DB::table('res_payment_methods')->insert(array(
            'payment_method' => 'Tarjeta',
            'created_at' => new DateTime
        ));
        DB::table('res_payment_methods')->insert(array(
            'payment_method' => 'Efectivo',
            'created_at' => new DateTime
        ));
    }

}

