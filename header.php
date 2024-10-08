<!DOCTYPE html>
<html lang="pt-br">
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-border-hrg">
        <div class="container-fluid">
            <a class="navbar-brand" href="http://10.1.1.31:80/centralservicos/" title="Central de Serviço">
                <img src="logoata.png" alt="Central de Serviço" style="width: 120px">
            </a> 
            
            <p class="titulo text-center" > 
                    <?php  echo isset($pageTitle) ? $pageTitle : "Dieta Pacientes Internados"; ?>
                </p>
                <div class="mb-3 text-center">
                <h5>Hora local</h5>
                <strong class="large-text"><span id="currentTime"></span></strong>
                <style>
                    .large-text {
                        font-weight: bold; 
                        font-size: 1.5em;  
                    }
                </style>
            </div>
            <script>
                function updateCurrentTime() {
                    const currentTimeElement = document.getElementById('currentTime');
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    currentTimeElement.textContent = `${hours}:${minutes}:${seconds}`;
                }
                setInterval(updateCurrentTime, 1000);
                updateCurrentTime();
                setInterval(() => {
                    location.reload();
                }, 300000); 
            </script>
        </div> 
      
             
       
    </nav>
    
</header>


   

