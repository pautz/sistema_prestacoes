  <?php
// Verificar se a conexão é segura (HTTPS)
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    // Gerar a URL para redirecionamento para HTTPS
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // Redirecionar para a versão HTTPS da URL atual
    header('Location: ' . $url);
    exit();
}

// Cabeçalhos de segurança
header('Strict-Transport-Security: max-age=31536000; includeSubDomains'); // HSTS
header('X-Content-Type-Options: nosniff'); // Proteção contra MIME Sniffing
header('X-Frame-Options: DENY'); // Proteger contra clickjacking
header('X-XSS-Protection: 1; mode=block'); // Proteção contra XSS
header('Referrer-Policy: no-referrer'); // Política de Referência
// Impedir que o site seja carregado em um iframe
header("X-Frame-Options: SAMEORIGIN");

// Você também pode adicionar segurança adicional
header("Content-Security-Policy: frame-ancestors 'self';");

header("Content-Security-Policy: frame-ancestors 'self' https://localhost.com;");
// Seu código PHP começa aqui
// ...

// Verifica se o cabeçalho enviado pelo CloudFront está presente
if (isset($_SERVER['HTTP_X_CUSTOM_HEADER'])) {
    $valorDoCabecalho = $_SERVER['HTTP_X_CUSTOM_HEADER'];

    // Exibe o valor do cabeçalho
    echo "Cabeçalho personalizado recebido: " . htmlspecialchars($valorDoCabecalho);
} else {
    // Caso o cabeçalho não exista
   
// Exibindo um botão com link específico



}

// Configura um cabeçalho de resposta (se necessário)
header("X-Powered-By: MeuServidorPHP");

?>
<?php
// Definindo os conteúdos como variáveis
$title1 = "Navegando por Novas Possibilidades";
$text1 = "Inspiramos e damos vida a ideias que refletem simplicidade e autenticidade. Nossa missão é explorar novos caminhos e transformar descobertas em experiências significativas.";

$title2 = "Conexões que Inspiram";
$text2 = "Vamos além do comum, criando vínculos autênticos que agregam valor e propósito. Acreditamos na importância de cada detalhe, com dedicação e cuidado.";

$title3 = "Detalhes que Transformam";
$text3 = "Nosso objetivo é criar algo único, que valorize a simplicidade e a conexão entre as pessoas, traduzindo ideias em experiências genuínas.";

?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Locação de Trator, Retroescavadeira e Equipamentos para Linha de Transmissão | Carlitos Locações</title>
    <meta name="description" content="Locação de tratores em Palmeira das Missões - RS. Encontre tratores de qualidade para sua necessidade! Trabalhamos com linha de transmissão, oferecendo equipamentos robustos e eficientes. Estamos na Av Independência, N 877, Sala 02, Palmeira Das Missões, Rio Grande Do Sul, Brasil. CEP: 98300-000. Acesse localhost.com">
    <meta name="keywords" content="trator, óleo, aeroportos, Palmeira das Missões, locação de tratores, linha de transmissão, locação de máquinas, retroescavadeiras, equipamentos pesados, aluguel de máquinas, sistema de controle de óleo, sistema de caixa, pagamentos de prestações, locação de quartos">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="index, follow">
  <meta name="author" content="Carlito Veeck Pautz Júnior">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
 

<style>
/* Definição geral do corpo */
body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.8;
            margin: 0;
            background-color: #e7e7e7;
            color: #333;
        }

        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Mantém o fundo atrás do conteúdo */
        }

/* Parágrafos e títulos */
p, h1, h2, h3, h4, h5, h6 {
    color: #000; /* Texto em preto */
}

/* Botões com estilo moderno */
button {
    background-color: #b0b0b0; /* Tom mais escuro para contraste */
    color: #fff; /* Texto branco */
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
}

button:hover {
    background-color: #8c8c8c; /* Fundo mais escuro no hover */
    color: #fff;
}

/* Carrossel */
.carousel-caption {
    background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente */
    padding: 10px;
}

.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
    display: block;
    height: auto;
    width: 100%;
    max-width: 100%;
    line-height: 1;
}

/* Imagens redondas */
.img-circle-custom {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
}

/* Fundo de seções */
.section {
    background: #e7e7e7; /* Fundo com a cor escolhida */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    color: #333; /* Texto em cinza escuro */
}

.section:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Ajustes para dispositivos móveis */
@media (max-width: 767px) {
    body {
        font-size: 18px; /* Tamanho da fonte menor */
        padding: 10px;
    }

    .img-circle-custom {
        width: 80px;
        height: 80px;
    }

    .carousel-inner > .item > img,
    .carousel-inner > .item > a > img {
        height: 300px; /* Ajuste para carrosséis menores */
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    body {
        font-size: 20px;
    }

    .img-circle-custom {
        width: 90px;
        height: 90px;
    }

    .carousel-inner > .item > img,
    .carousel-inner > .item > a > img {
        height: 420px;
    }
}

/* Iframes responsivos */
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* Proporção 16:9 */
    height: 0;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Botões fixados na parte inferior */
.stylized-button {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    font-size: 16px;
    color: #fff;
    background-color: #b0b0b0; /* Tom escuro para destacar */
    border: none;
    padding: 10px 20px;
    text-align: center;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.stylized-button:hover {
    background-color: #8c8c8c;
}

/* Elementos específicos de navegação */
.navbar {
    padding-top: 3px;
    padding-bottom: 3px;
    border: 0;
    border-radius: 0;
    margin-bottom: 0;
    font-size: 11px;
    letter-spacing: 5px;
}

.navbar-nav li a:hover {
    color: #1abc9c !important;
}

/* Contêiner principal para PDF estilo slide */
.pdf-slide-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* PDF individual como parte de um slide */
.pdf-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: transform 0.5s ease-in-out;
}
</style>


</head>
<body>
  

<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="../">Carlito's Locações</a>
      <a class="navbar-brand" href="tel:+5555996479747">(55) 9.9647-9747</a>
      
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/site/login.php">Entrar</a></li>
        </ul>
    </div>
  </div>
</nav>
<div id="particles-js"></div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
   
<header class="container-fluid bg-1 text-center">
        <img src="/site2/carlitoschapeu.png" class="title-img" width="400" height="400" alt="Carlitos Chapéu">
        <img src="/site2/fontcarlitos.png" class="title-img" width="400" height="400" alt="Fonte Carlitos">
    </header>


<center><h1>Locação de Equipamentos & Prestação de Serviços</h3><br>
  
  
    <ul>
   <li><a href="https://localhost.com/site3/cadastro_produto/">Cadastro de Locações</a></li>
   <li><a href="https://localhost.com/site2/nossasmaquinas/">Locações</a></li>
   <li><a href="https://localhost.com/site3/remover_produto/">Remover Locações</a></li>
   <li><a href="https://localhost.com/site3/cadastro_produto/cadastro_quarto.php">Cadastro de Quartos</a></li>
   <li><a href="https://localhost.com/site2/nossasmaquinas/quartosroom.php">Quartos</a></li>
    <li><a href="https://localhost.com/site3/cadastro_produto/cadastro_voo.php">Oferecer Carona</a></li>
    <li><a href="https://localhost.com/site2/nossasmaquinas/voos.php">Pegar Carona</a></li>
        <li><a href="https://localhost.com/caixa.php" class="button">Sistema Caixa</a></li>
    <li><a href="https://localhost.com/registros_oil.php" class="button">Registros de Óleo</a></li>
    </ul>

      <br>
      
<div class="responsive-container">
    <a href="https://localhost.com/contato/" target="_blank" class="stylized-button">Contato</a><br>
    <form method="GET" action="../site/msg.php" class="responsive-form">
        <label for="id">Defina um Contrato:</label><br>
        <input type="text" id="id" name="id" placeholder="Digite o Contrato." required>
        <button type="submit">Buscar</button>
    </form>
    <form method="GET" action="../site/pageloca.php" class="responsive-form">
        <label for="cv">Pesquisar Contrato:</label><br>
        <input type="text" id="cv" name="cv" placeholder="Digite o Contrato." required>
        <button type="submit">Buscar</button>
    </form>
    <form method="GET" action="https://localhost.com/site/page.php?id=&" class="responsive-form">
        <label for="id">Pesquisar Prestação:</label><br>
        <input type="text" id="id" name="id" placeholder="Digite o número da prestação." required>
        <button type="submit">Buscar</button>
    </form>
</div>

<br>
</center>

<div class="container-fluid bg-3 text-center">
 <div class="section">
        <h2><?php echo $title1; ?></h2>
        <p><?php echo $text1; ?></p>
    </div>

    <div class="section">
        <h2><?php echo $title2; ?></h2>
        <p><?php echo $text2; ?></p>
    </div>

    <div class="section">
        <h2><?php echo $title3; ?></h2>
        <p><?php echo $text3; ?></p>
    </div>

</div>
    
 
<div id="myCarousel" class="carousel slide bg-3 text-center">
  <div class="carousel-inner">
    <div class="item">
     
  <img src="allmaq/t3.png" alt="Trator"></a>
      
    </div>
    <div class="item">
      
  <img src="https://localhost.com/site2/tratorbutton.png" alt="trator">
  </a>
    </div>
    <div class="item">
        
  <img src="https://localhost.com/cia2.jpg" alt="Cia"></a>
          </div>
          
    <div class="item active">
     
  <img src="https://localhost.com/cia1.jpg" alt="Cia"></a>
          </div>
  </div>    
  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#myCarousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
<div class="video-container">
        <iframe src="https://www.youtube.com/embed/7BCKgyU3JF0?si=hqKeq0BW2OigMgDF" frameborder="0" allowfullscreen></iframe>
    </div>
<center><div class="slider-container">
        <canvas id="pdf-render"></canvas>
        <div class="navigation">
            <button id="prev" disabled>❮</button>
            <button id="next">❯</button>
        </div>
    </div>
</center>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
    <script>
       const url = 'https://localhost.com/site2/Um_Software_de_Transporte1.pdf'; // Substitua pelo caminho do seu arquivo PDF
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            canvas = document.getElementById('pdf-render'),
            ctx = canvas.getContext('2d');

        // Renderiza a página do PDF
        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then((page) => {
                const scale = window.innerWidth / 800; // Adapta a escala ao tamanho da tela
                const viewport = page.getViewport({ scale });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                };
                const renderTask = page.render(renderContext);

                renderTask.promise.then(() => {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            updateNavigationButtons();
        }

        // Atualiza os botões de navegação
        function updateNavigationButtons() {
            document.getElementById('prev').disabled = pageNum <= 1;
            document.getElementById('next').disabled = pageNum >= pdfDoc.numPages;
        }

        // Controla mudança de página
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        document.getElementById('prev').addEventListener('click', onPrevPage);
        document.getElementById('next').addEventListener('click', onNextPage);

        // Carrega o PDF e exibe a primeira página
        pdfjsLib.getDocument(url).promise.then((pdfDoc_) => {
            pdfDoc = pdfDoc_;
            renderPage(pageNum);
        });

        // Ajusta o tamanho do PDF quando a tela for redimensionada
        window.addEventListener('resize', () => {
            queueRenderPage(pageNum);
        });
    </script>
<!-- Footer -->
<footer class="container-fluid bg-4 text-center">
<a href="https://localhost.com/site/login.php">Entrar</a>


<p>Apoie o projeto na Binance:</p>
<img src="https://localhost.com/binanceiota.jpg" style="border: 0; width: 128px; height: 128px" alt="QRCODE apoie ID Barbante.">
 <img src="//ipv6.he.net/certification/create_badge.php?pass_name=carlitopautz&amp;badge=1" style="border: 0; width: 128px; height: 128px" alt="Selo de certificação IPv6 para Carlito Pautz">
 <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="http://localhost.com/site2">Carlito's Locações</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="http://localhost.com/pofft">Carlito Veeck Pautz Júnior</a> is licensed under <a href="https://creativecommons.org/licenses/by/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">Creative Commons Attribution 4.0 International<img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1" alt=""><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1" alt=""></a></p>
<p>Av Independência, N 877, Sala 02, Palmeira Das Missões, Rio Grande Do Sul, Brazil 98300-000</p>
 <p>Desenvolvido por Carlito Veeck Pautz Júnior.</p> 
 </footer>

</body>
</html>
 <script>
        /* Configuração do Particles.js */
        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 100,
                    density: { enable: true, value_area: 800 }
                },
                color: { value: "#333333" }, // Cor escura para as partículas
                shape: {
                    type: "circle",
                    stroke: { width: 0, color: "#000000" },
                    polygon: { nb_sides: 5 }
                },
                opacity: {
                    value: 0.7, // Transparência das partículas
                    random: false,
                    anim: { enable: false, speed: 1, opacity_min: 0.1, sync: false }
                },
                size: {
                    value: 5,
                    random: true,
                    anim: { enable: false, speed: 40, size_min: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#333333", // Cor escura para as linhas
                    opacity: 0.5, // Transparência das linhas
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 6,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                    attract: { enable: false, rotateX: 600, rotateY: 1200 }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "grab" },
                    onclick: { enable: true, mode: "push" },
                    resize: true
                },
                modes: {
                    grab: { distance: 200, line_linked: { opacity: 1 } },
                    bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 },
                    repulse: { distance: 200, duration: 0.4 },
                    push: { particles_nb: 4 },
                    remove: { particles_nb: 2 }
                }
            },
            retina_detect: true
        });
    </script>
