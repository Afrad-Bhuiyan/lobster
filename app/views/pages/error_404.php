
<?php 
    $config=new config;
    $functions=new functions;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <!--Font Aweseme-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type='text/css' media='all'>
    <!--Boootstrap css-->
    <link rel="stylesheet" href="<?php echo "{$config->domain('assets/css/bootstrap.min.css')}" ?>" type='text/css' media='all'>
    <!--Error css-->
    <link rel="stylesheet" href="<?php echo "{$config->domain('assets/css/error_pages.min.css')}" ?>">


</head>
<body>
    

    <main>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>404 Not found</h3>
                        <p>
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit in dolore esse provident asperiores quaerat quo temporibus rerum libero sint laborum nulla, voluptatum, eaque et harum alias obcaecati cumque fuga.
                        </p>
                        <a href="<?php echo $config->domain(); ?>" class="btn btn-primary">
                            Return to home
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>




</body>
</html>