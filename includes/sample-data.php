<?php
/*
Purpose: Prevents direct access to the file. The check ensures that the file is only executed within the WordPress environment 
(not directly accessed via the browser).
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WP_InShape_Sample_Data {

    public function __construct() {

        add_action( 'wp_ajax_inshape_generate_csv', [$this,'generate_csv_file_callback' ]);
        add_action( 'wp_ajax_nopriv_inshape_generate_csv', [$this,'generate_csv_file_callback' ]); //for non logged in users
        add_action( 'wp_ajax_inshape_import_csv', [$this,'import_data_from_csv' ]);
    }
    
    function generate_csv_file_callback() {
        
        // Check nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'inshape_generate_csv_nonce_76543' ) ) {
            wp_send_json_error( 'Invalid nonce' );
            wp_die();
        }
    
        // Generate CSV data
        $data = [];
        $header = ['client name', 'race', 'gender', 'age', 'unit', 'weight', 'height', 'waist circumference', 'lost wait goal', 'activity level', 'weekly activity description'];
        $data[] = $header;


        for ($i = 0; $i < 200; $i++) {
            $unit = mt_rand(0,1) ? 'imperial' : 'metric';
            $gender = mt_rand(0,1) ? 'Male' : 'Female';
            $activityLevel = ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])];
            $age = mt_rand(18, 65);
            $weight = $this->generateRandomWeight($unit);
            $height = $this->generateRandomHeight($unit);
            $waist = $this->generateRandomWaist($unit, $weight);
            $goal = $this->generateRandomGoal($unit);
            $description = "Exercises 3-5 days a week";

            $row = [
                $this->generateRandomName(),
                $this->generateRandomEthnicity(),
                $gender,
                $age,
                $unit,
                $weight,
                $height,
                $waist,
                'Loose the following percentage of body fat: '.$goal,
                $activityLevel,
                $description
            ];
            $data[] = $row;
        }

        // Output CSV
        // Create dynamic filename with date and time
        $timestamp = date('YmdHis');
        $filename = INSHAPE_DATA_DIR . 'fitness_data_' . $timestamp . '.csv';
        $file = fopen($filename, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
  
        // Send JSON response
        $response = [
            'success' => true,
            'data' => "CSV file '$filename' generated successfully.",
        ];

        wp_send_json_success( $response );
        wp_die();

    }
    

    // Function to generate a random name
    function generateRandomName() {
        $firstNames = [
            "Aaliyah", "Aaron", "Abigail", "Adam", "Adrian", "Adriana", "Aidan", "Alan", "Alana", "Albert",
            "Alberto", "Alexander", "Alexandra", "Alex", "Alfred", "Alice", "Alicia", "Allen", "Allison", "Alonzo",
            "Alyssa", "Amanda", "Amber", "Amelia", "Amy", "Andrew", "Andrea", "Angel", "Angela", "Angelo",
            "Angus", "Anna", "Anthony", "Antonio", "April", "Arabella", "Archie", "Arthur", "Audrey", "Augustine",
            "Austin", "Ava", "Avery", "Axel", "Barbara", "Barry", "Beatrice", "Beau", "Beckett", "Bella",
            "Benjamin", "Bennett", "Bernadette", "Bernard", "Bertram", "Bethany", "Betty", "Beverly", "Bianca", "Bill",
            "Billy", "Blake", "Blanche", "Bob", "Bonnie", "Bradley", "Brandon", "Brenda", "Brian", "Brianna",
            "Bridget", "Britney", "Brittany", "Brock", "Bruce", "Bryan", "Bryce", "Caleb", "Cameron", "Camila",
            "Camille", "Candace", "Candice", "Carlos", "Carmen", "Caroline", "Carolyn", "Carroll", "Carter", "Cassandra",
            "Catherine", "Cathy", "Cecilia", "Cedric", "Celeste", "Chad", "Chance", "Chanel", "Charity", "Charles",
            "Charlotte", "Chase", "Chelsea", "Cheryl", "Chester", "Chloe", "Chris", "Christian", "Christina", "Christopher",
            "Claire", "Clara", "Clarence", "Clark", "Claude", "Claudia", "Clay", "Clementine", "Clifford", "Clint",
            "Clive", "Cody", "Cole", "Colin", "Colleen", "Collin", "Colt", "Connor", "Connie", "Cooper",
            "Corey", "Cornelius", "Corrine", "Courtney", "Craig", "Crispin", "Crystal", "Curtis", "Cynthia", "Dale",
            "Dallas", "Dalton", "Dana", "Daniel", "Danielle", "Danny", "Darlene",  "Darren", "Darryl", "Daryl",
            "David", "Dawn", "Dean", "Deborah", "Debra", "Declan", "Deirdre", "Delia", "Dennis", "Derek",
            "Derrick", "Destiny", "Devon", "Diana", "Diane", "Diego", "Dillon", "Dina", "Dominic", "Dominique",
            "Donald", "Donna", "Donnie", "Dora", "Doris", "Dorothy", "Douglas", "Drew", "Duane", "Dustin",
            "Dwayne", "Dylan", "Edith", "Edmund", "Edward", "Edwin", "Eleanor", "Eli", "Elias", "Elijah",
            "Elizabeth", "Ella", "Ellen", "Ellie", "Elliot", "Ellis", "Elmer", "Elmira", "Eloise", "Elsa",
            "Emerson", "Emily", "Emma", "Emmett", "Eric", "Erica", "Erik", "Erin", "Ernest", "Esmeralda",
            "Esther", "Ethan", "Eugene", "Eula", "Eva", "Evelyn", "Everett", "Faith", "Fanny", "Farrah",
            "Faustina", "Felix", "Ferdinand", "Fiona", "Florence", "Floyd", "Forrest", "Frances", "Francis", "Francisco",
            "Frederick", "Gabriel", "Gail", "Garrett", "Gary", "Gavin", "Gene", "Geneva", "Geoffrey", "George",
            "Georgina", "Gerald", "Geraldine", "Gerard", "Gertrude", "Gideon", "Gilbert", "Gilberto", "Gillian", "Gina",
            "Ginger", "Gladys", "Glen", "Glenn", "Gloria", "Grace", "Graham", "Grant",  "Gregory", "Greta",
            "Griffin", "Guillermo", "Guinevere", "Gus", "Gwen", "Hadley", "Haley", "Hannah", "Harold", "Harriet",
            "Harry", "Harvey", "Hayden", "Hazel", "Heath", "Hector", "Heidi", "Helen", "Helena", "Helene",
            "Henry", "Herbert", "Hermoine", "Hilary", "Hillary", "Holly", "Homer", "Hope", "Howard", "Hubert",
            "Hugh", "Hugo", "Hunter", "Ian", "Ida", "Ignatius", "Igor", "Imogen", "Imogene", "Ingrid",
            "Ira", "Irene",  "Iris", "Isaac", "Isabel", "Isabella", "Isabelle", "Isaiah", "Ivan", "Jack",
            "Jackie", "Jackson", "Jacob", "Jacqueline", "Jake", "James", "Jamie", "Jan", "Jane", "Janet",
            "Janice", "Jared", "Jasmine", "Jason", "Javier", "Jaxon", "Jean", "Jeanie", "Jeff", "Jeffery",
            "Jeffrey", "Jenna", "Jennifer", "Jenny", "Jeremiah", "Jeremy", "Jerome", "Jerry", "Jesse", "Jessica",
            "Jessie", "Jill", "Jim", "Jimmy", "Joan", "Joann", "Joanna", "Joanne", "Joaquin", "Jody",
            "Joe", "Joel", "Joey", "Johan", "John", "Johnny", "Johnson", "Jon", "Jonah", "Jonas",
            "Jonathan", "Jordan", "Jorge", "Jose", "Josefina", "Joseph", "Josephine", "Joshua", "Josiah", "Joyce",
            "Juan", "Juanita", "Jude", "Judith", "Judy", "Julia", "Julian", "Julie", "Julius", "June",
            "Justin", "Kaitlyn", "Karen", "Karin", "Karl", "Kate", "Katherine", "Kathleen", "Kathryn", "Kathy",
            "Katie", "Kayla", "Kaylee", "Keira", "Keith", "Kelly", "Kelsey", "Kendall", "Kenneth", "Kenny",
            "Kent", "Kevin", "Kieran", "Kim", "Kimberly", "King", "Kira", "Kirsten", "Kitty", "Klaus",
            "Kurt", "Kyle", "Lacey", "Lamar", "Lambert", "Lance", "Larry", "Laura", "Lauren", "Laurence",
            "Laurie", "Lawrence", "Leah", "Leander", "Leila", "Leif", "Leigh", "Lena", "Leonard", "Leon",
            "Leopold", "Leroy", "Leslie", "Lester", "Leticia", "Lewis", "Liam", "Lila", "Lillian", "Lily",
            "Lincoln", "Linda", "Lindsay", "Lindsey", "Lisa", "Liz", "Liza", "Lloyd", "Logan", "Lois",
            "Lola", "London", "Lorraine", "Lou", "Louis", "Louisa", "Louise", "Luca", "Lucas", "Lucian",
            "Lucy", "Luis", "Luke", "Lula", "Luna", "Luther", "Lydia", "Lyla", "Lynn", "Mabel",
            "Madeline", "Madelyn", "Madison", "Mae", "Maggie", "Magnus", "Malcolm", "Malorie", "Manuel", "Mara",
            "Marcel", "Marcia", "Margaret", "Margarita", "Marge", "Maria", "Mariah", "Marie", "Marilyn", "Marina",
            "Mario", "Marion", "Mark", "Marla", "Marlene", "Marley", "Marlon", "Marsha", "Marshall", "Martha",
            "Martin", "Marty", "Marvin", "Mary", "Mason", "Mateo", "Matilda", "Matthew", "Maurice", "Maureen",
            "Max", "Maxwell", "Maya", "Maybelle", "Megan", "Melanie", "Melissa", "Melvin", "Mercedes", "Meredith",
            "Merle", "Mia", "Michael", "Michelle", "Miguel", "Mike", "Mildred", "Miles", "Millicent", "Miller",
            "Milo", "Milton", "Mina", "Minerva", "Minnie", "Miranda", "Miriam", "Mitchell", "Molly", "Monica",
            "Morgan", "Morris", "Morton", "Moses", "Murray", "Myrtle", "Nadia", "Nancy", "Naomi", "Nathan",
            "Nathaniel", "Natalie", "Natasha", "Neal", "Ned", "Neil", "Nelson", "Nelly", "Nena", "Nero",
            "Nestor", "Neville", "Nicholas", "Nicole", "Nigel", "Nina", "Noah", "Noel", "Nolan", "Nora",
            "Norman", "Oliver", "Olivia", "Opal", "Orlando", "Oscar", "Osborne", "Owen", "Paige", "Pamela",
            "Pandora", "Parker", "Patrick", "Paul", "Paula", "Pauline", "Payton", "Pearl", "Pedro", "Peggy",
            "Penelope", "Percy", "Perry", "Peter", "Petra", "Philip", "Phillip", "Phoebe", "Phyllis", "Pierce",
            "Pippa", "Polly", "Preston", "Quentin", "Quincy", "Rachel", "Rafael", "Ralph", " Ramona", "Randall",
            "Randolph", "Randy", "Raphael", "Ray", "Raymond", "Rebecca", "Rebekah", "Regina", "Regine", "Reginald",
            "Renee", "Rene", "Rhea", "Rhett", "Rhonda", "Ricardo", "Richard", "Rick", "Ricky", "Riley",
            "Rita", "River", "Roberto", "Robin", "Rob", "Rochelle", "Rodger", "Rodney", "Roger", "Roland",
            "Rolf", "Roman", "Ron", "Ronald", "Ronnie", "Rosa", "Rosalind", "Rosalie", "Rosalyn", "Rose",
            "Rosemarie", "Rosemary", "Rosie", "Ross", "Rowan", "Roxanne", "Roy", "Ruben", "Ruby", "Rufus",
            "Rupert", "Russell", "Ruth", "Ryan", "Sabrina", "Sally", "Sam", "Samantha", "Samuel", "Sandra",
            "Sandy", "Sarah", "Sasha", "Saul", "Savannah", "Scarlett", "Scott", "Sebastian", "Selma", "Serena",
            "Seth", "Seymour", "Shane", "Shannon", "Shaun", "Shawn", "Sheldon", "Shelley", "Shelly", "Shenandoah",
            "Sheri", "Sheridan", "Sherrie", "Sherry", "Shirley", "Sidney", "Sigrid", "Silas", "Simon", "Simone",
            "Simeon", "Sister", "Skye", "Sofia", "Sol", "Solomon", "Sophia", "Sophie", "Stacey", "Stacy",
            "Stanley", "Stefan", "Stella", "Stephen",  "Stephanie", "Steve", "Steven", "Stewart", "Susan", "Suzanne",
            "Susie", "Sven", "Sylvia", "Tabitha", "Tad", "Talia", "Tanner", "Tara", "Tasha", "Tate",
            "Taylor", "Ted", "Teddy", "Teresa", "Terrance", "Terrence", "Terry", "Tessa", "Thelma", "Theo",
            "Theodore", "Theresa", "Thomas", "Tiffany", "Tim", "Timothy", "Tina", "Toby", "Todd", "Tom",
            "Tommy", "Toni", "Tony", "Tracey", "Tracy", "Travis", "Trent", "Trevor", "Tristan", "Troy",
            "Trudy", "Turner", "Tyler", "Ulysses", "Ursula", "Valentine", "Valerie", "Vanessa", "Vera", "Veronica",
            "Victor", "Victoria", "Vincent", "Viola", "Violet", "Virginia", "Vivian", "Wade", "Wagner", "Wallace",
            "Walter", "Wanda", "Warren", "Wayne", "Wendy", "Werner", "Wesley", "Wilbur", "Wilfred", "Wiley",
            "Wilhelm", "Will", "William", "Willie", "Willow", "Wilson", "Winifred", "Winston", "Wyatt", "Xavier",
            "Xena", "Yara", "Yasmine", "Yehuda", "Zachary", "Zachery", "Zackary", "Zelda", "Zoe", "Zola"
        ];
        $lastNames = [
            "Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "Garcia", "Rodriguez", "Wilson",
            "Martinez", "Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin", "Jackson", "Thompson", "White",
            "Lopez", "Lee", "Gonzalez", "Harris", "Clark", "Lewis", "Robinson", "Walker", "Perez", "Hall",
            "Young", "Allen", "King", "Wright", "Scott", "Green", "Baker", "Adams", "Nelson", "Carter",
            "Mitchell", "Perez", "Roberts", "Turner", "Phillips", "Campbell", "Parker", "Evans", "Edwards", "Collins",
            "Stewart", "Sanchez", "Morris", "Rogers", "Reed", "Cook", "Morgan", "Bell", "Murphy", "Bailey",
            "Rivera", "Cooper", "Richardson", "Cox", "Howard", "Ward", "Torres", "Peterson", "Gray", "Ramirez",
            "James", "Watson", "Brooks", "Kelly", "Sanders", "Price", "Bennett", "Wood", "Barnes", "Ross",
            "Henderson", "Coleman", "Jenkins", "Perry", "Powell", "Long", "Patterson", "Hughes", "Flores", "Washington",
            "Butler", "Simmons", "Foster", "Gonzales", "Bryant", "Alexander", "Russell", "Griffin", "Diaz", "Hayes",
            "Myers", "Ford", "Hamilton", "Graham", "Sullivan", "Wallace", "Woods", "Cole", "West", "Jordan",
            "Owens", "Reynolds", "Fisher", "Ellis", "Harrison", "Gibson", "McDonald", "Cruz", "Marshall", "Ortiz",
            "Gomez", "Murray", "Freeman", "Wells", "Webb", "Simpson", "Stevens", "Tucker", "Porter", "Hunter",
            "Hicks", "Crawford", "Henry", "Boyd", "Mason", "Morales", "Kennedy", "Warren", "Dixon", "Ramos",
            "Reyes", "Burns", "Gordon", "Shaw", "Holmes", "Rice", "Robertson", "Hunt", "Black", "Daniels",
            "Snyder", "Larson", "Elliott", "Chavez", "Flores", "Wilkinson", "Nichols", "Banks", "Meyer", "Fernandez",
            "Garcia", "Rodriguez", "Wilson", "Martinez", "Anderson", "Taylor", "Thomas", "Hernandez", "Moore", "Martin",
            "Jackson", "Thompson", "White", "Lopez", "Lee", "Gonzalez", "Harris", "Clark", "Lewis", "Robinson"
        
        ];
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    // Function to generate a random race (simplified for this example)
    function generateRandomEthnicity() {
        $ethnicity = [
            "Korean American",
            "Vietnamese American",
            "Mexican American" ,
            "Puerto Rican American",
            "Cuban American",
            "Dominican American",
            "Central American", 
            "South American", 
            "Caribbean American", 
            "European American", 
            "Middle Eastern American", 
            "Pacific Islander American", 
            "African American",
            "White American",
            "Other" => "Other",
        ];
        return $ethnicity[array_rand($ethnicity)];
    }

    // Function to generate random weight (kg or lbs)
    function generateRandomWeight($unit) {
        if ($unit == 'imperial') {
            return mt_rand(100, 250); // lbs
        } else {
            return mt_rand(45, 115); // kg
        }
    }

    // Function to generate random height (cm or inches)
    function generateRandomHeight($unit) {
        if ($unit == 'imperial') {
            return mt_rand(5*12, 6*12 + 6); // inches (5'0" to 6'6")
        } else {
            return mt_rand(152, 198); // cm (approx 5'0" to 6'6")
        }
    }

    // Function to generate random waist circumference (cm or inches)
    function generateRandomWaist($unit, $weight) {
        //Simple approximation, adjust as needed
        $base = ($unit == 'imperial') ? 30 : 76;
        $modifier = ($unit == 'imperial') ? $weight / 30 : $weight / 15; //Make it dependent on weight
        return mt_rand(floor($base), floor($base + $modifier));
    }

    // Function to generate random goal weight loss (lbs or kg)
    function generateRandomGoal($unit){
        if ($unit == 'imperial'){
            return mt_rand(5,30); //lbs
        } else {
            return mt_rand(2,13); //kg
        }
    }

    /**
     * Imports client data from a CSV file into WordPress posts and post meta.
     *
     * @param string $csv_filepath The path to the CSV file.
     * @return bool True on success, false on failure.  Displays error messages if there are problems.
     */
    function import_data_from_csv( ) {
    
        // Check nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'inshape_import_csv_nonce_28017' ) ) {
            wp_send_json_error( 'Invalid nonce' );
            wp_die();
        }

        if ( ! isset($_POST['csv_file'])  ) {
            error_log( 'Error: CSV file not provided.');
            return false;
        }
       
        $csv_file =  $_POST['csv_file'];
        error_log(print_r($csv_file,true));

        if ( ! file_exists( $csv_file ) ) {
            error_log( 'Error: CSV file not found at ' . $csv_file );
            return false;
        }
    
        if ( ! $handle = fopen( $csv_file, 'r' ) ) {
            error_log( 'Error: Could not open CSV file.' );
            return false;
        }
    
        //Skip header row
        fgetcsv( $handle );
    
        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            //Sanitize data (this is CRUCIAL to prevent vulnerabilities!)
            $client_name = sanitize_text_field( $row[0] );
            $ethnicity        = sanitize_text_field( $row[1] );
            $gender      = sanitize_text_field( $row[2] );
            $age         = intval( sanitize_text_field( $row[3] ) ); //Convert to integer
            $unit        = sanitize_text_field( $row[4] );
            $weight      = floatval( sanitize_text_field( $row[5] ) ); // Convert to float
            $height      = floatval( sanitize_text_field( $row[6] ) ); // Convert to float
            $waist       = floatval( sanitize_text_field( $row[7] ) ); // Convert to float
            $goal        = sanitize_text_field( $row[8] );
            $activity    = sanitize_text_field( $row[9] );
            $activity_description = sanitize_text_field( $row[10] );
    
            // Create the post
            $post_id = wp_insert_post( array(
                'post_title'    => $client_name,
                'post_type'     => 'inshape',
                'post_status'   => 'draft', // Or 'draft' if you want to review them first
                'post_author'   => get_current_user_id(),

            ) );
    
            if ( is_wp_error( $post_id ) ) {
                error_log( 'Error creating post for ' . $client_name . ': ' . $post_id->get_error_message() );
                continue; //Skip to the next row if there's an error
            }
    
            // Add post meta

            update_post_meta( $post_id, 'inshape_ethnicity_field', $ethnicity );
            update_post_meta( $post_id, 'inshape_gender_field', $gender );
            update_post_meta( $post_id, 'inshape_age_field', $age );
            update_post_meta( $post_id, 'inshape_units_field', $unit );
            update_post_meta( $post_id, 'inshape_weight_field', $weight );
            update_post_meta( $post_id, 'inshape_height_field', $height );
            update_post_meta( $post_id, 'inshape_waist_field', $waist );
            update_post_meta( $post_id, 'inshape_goal_description_field', $goal );
            update_post_meta( $post_id, 'inshape_activity_field', $activity );
            update_post_meta( $post_id, 'inshape_activity_description_field', $activity_description );
        }
    
        fclose( $handle );
        return true;
    }
    

}

/*
Purpose: Creates an instance of the WP_InShape_Admin_UI class.
*/
new WP_InShape_Sample_Data();