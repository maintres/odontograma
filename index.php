<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Odontograma Interactivo</title>    
        <!-- Bootstrap CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <!-- iconos -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Librerías JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <style>
            input, textarea {
                border: 1px solid #6c757d !important;
            }
            .controls {
                display: flex;
                justify-content: center;
                margin-bottom: 20px;
                flex-wrap: wrap;
                gap: 10px;
            }
            .control-btn {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .control-btn .color-indicator {
                width: 15px;
                height: 15px;
                border: 1px solid #333;
                border-radius: 3px;
            }            
            /* Colores para los tratamientos en los botones */
            :root {
                --existente-color: #e74c3c;
                --requerida-color: #3498db;
                --ausente-color: #f1f1f1;
                --corona-color: #f1c40f;
            }            
            .control-btn[data-treatment="existente"] .color-indicator { background-color: var(--existente-color); }
            .control-btn[data-treatment="requerida"] .color-indicator { background-color: var(--requerida-color); }

            .control-btn[data-treatment="ausente"] .color-indicator { background-color: var(--ausente-color); position: relative; }
            .control-btn[data-treatment="extraido"] .color-indicator { background-color: var(--ausente-color); position: relative; }
            .control-btn[data-treatment="extraer"] .color-indicator { background-color: var(--ausente-color); position: relative; }
            .control-btn[data-treatment="fija"] .color-indicator { background-color: var(--ausente-color); position: relative; }
            .control-btn[data-treatment="removible"] .color-indicator { background-color: var(--ausente-color); position: relative; }
            /* -------------------- */
            .control-btn[data-treatment="corona"] .color-indicator { background-color: var(--corona-color); }
            .control-btn[data-treatment="limpiar"] .color-indicator { background-color: #f1f1f1; position: relative; }
            /* ----------Estilos de los botones--------- */
            .control-btn[data-treatment="limpiar"] .color-indicator:after,
            .control-btn[data-treatment="ausente"] .color-indicator:after,
            .control-btn[data-treatment="extraido"] .color-indicator:after,
            .control-btn[data-treatment="extraer"] .color-indicator:after,
            .control-btn[data-treatment="fija"] .color-indicator:after,
            .control-btn[data-treatment="removible"] .color-indicator:after {            
                position: absolute;         
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 12px;
                font-weight: bold;           
            }
            .control-btn[data-treatment="limpiar"] .color-indicator:after{
                content: "🗑️";
            }
            .control-btn[data-treatment="ausente"] .color-indicator:after{          
                content: "A";
                color:#000;/*negro*/
            }
            .control-btn[data-treatment="extraido"] .color-indicator:after{
                content: "X";
                color:#FF0000;/*rojo*/             
            }
            .control-btn[data-treatment="extraer"] .color-indicator:after{          
                content: "X";
                color:#0000ff;/*azul*/
            }
            .control-btn[data-treatment="fija"] .color-indicator::after {
                content: "";
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid #FF0000; /* Borde azul */
                background-color: white;   /* Centro blanco */
                box-sizing: border-box;
            }
            .control-btn[data-treatment="removible"] .color-indicator::after {
                content: "";
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid #0000ff; /* Borde azul */
                background-color: white;   /* Centro blanco */
                box-sizing: border-box;
            }
            .odontograma {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                margin: 20px 0;
            }
            /* estilo para cuadrar las zonas */
            .arch {
                display: flex;
                align-items: center;
                width: auto;
                margin: 0;
                padding: 4px;
            }
            @media print {
                div .row #zona-1-2-3-4 {
                    flex-wrap:nowrap;
                }
                .rows-container{
                    flex-wrap:nowrap;
                }
            }        
            /* ------------------ */
            .tooth-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 0 5px;
            }
            .tooth-number {
                font-size: 15px;
                margin-bottom: 5px;
            }
            .tooth {
                width: 40px;
                height: 50px;
                position: relative;
                cursor: pointer;
            }
            
            /* SVG dentro del diente */
            .tooth svg {
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                left: 0;
            }
            .tooth-part {
                cursor: pointer;
                stroke: black;
                stroke-width: 1;
                fill: #f1f1f1;
            }
            
            /* X para diente ausente */
            .tooth-ausente .x-mark {
                display: block;
                border: 2px solid black;
            }
            .x-mark {
                display: none;
                pointer-events: none;
                font-weight: bold;
            }            
            /* Colores para los tratamientos */
            .existente { fill: var(--existente-color) !important; }
            .requerida { fill: var(--requerida-color) !important; }
            .ausente { fill: var(--ausente-color) !important; }
            .corona { fill: var(--corona-color) !important; }            
            /* Borde vertical solo entre columnas */
            .row .arch + .arch {
                border-left: 2px solid black;
            }            
            /* Borde horizontal solo entre filas */
            .rows-container .row + .row .arch {
                border-top: 2px solid black;
            }
            /* Canvas oculto para la exportación */
            #export-canvas {
                display: none;
            }                   
            .valor-cantidad {
                border: 1px solid black;
                padding: 2px 5px;
            }
        </style>
    </head>

    <body class="bg-secondary">    
        <div class="container py-4 border bg-white">
            <h1 class="text-center mb-4">Odontograma Interactivo</h1>        
            <!-- Información del paciente -->
            <div class="row mb-1">
                <div class="col-md-8">
                    <div class="mb-1 d-flex w-100 ">
                        <label for="patient-name" class="form-label me-2">Nombre y Apellido:</label>
                        <input type="text" class="form-control w-75" id="patient-name" placeholder="Nombre del paciente">
                    </div>
                </div>       
                <div class="col-md-4">
                    <div class="mb-3 d-flex">
                        <label for="exam-date" class="form-label">Fecha de Examen:</label>
                        <input type="date" class="form-control w-50 ms-2" id="exam-date" >
                    </div>
                </div>
            </div>
            <div class="row mb-1">            
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="birth-date" class="form-label me-2">Fecha de Nacimiento:</label>
                        <input type="date" class="form-control w-50" id="birth-date">
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="tel" class="form-label me-2">Teléfono:</label>
                        <input type="number" class="form-control ms-2" id="tel">
                    </div>
                </div> 
            </div>
            <div class="row mb-1">            
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="domicilio" class="form-label">Domicilio:</label>
                        <input type="text" class="form-control ms-2" id="domicilio">
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="localidad" class="form-label">Localidad:</label>
                        <input type="text" class="form-control ms-2" id="localidad">
                    </div>
                </div> 
            </div>
            <!-- ---------------------------------------- -->
            <div class="card">
                <div class="card-body">
                    <!-- Controles de tratamiento -->
                    <div class="row ">
                        <div class="controls mb-4">
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="existente">
                                <div class="color-indicator"></div>
                                <span>Prestaciones Existentes</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="requerida">
                                <div class="color-indicator"></div>
                                <span>Prestaciones Requeridas</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="ausente">
                                <div class="color-indicator"></div>
                                <span>Ausente</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="extraido">
                                <div class="color-indicator"></div>
                                <span>Diente Extraido</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="extraer">
                                <div class="color-indicator"></div>
                                <span>Diente a Extraer</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="fija">
                                <div class="color-indicator"></div>
                                <span>Protesis Fija</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="removible">
                                <div class="color-indicator"></div>
                                <span>Protesis Removible</span>
                            </button>
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="corona">
                                <div class="color-indicator"></div>
                                <span>Corona</span>
                            </button>
                            
                            <button class="btn btn-light control-btn border border-secondary" data-treatment="limpiar">
                                <div class="color-indicator"></div>
                                <span>Limpiar</span>
                            </button>
                        </div>
                    </div>             
                    <!-- Odontograma y Referencias -->
                    <div class="row">
                        <!-- Odontograma -->
                        <div class="col-lg-9">
                            <div class="odontograma" id="odontograma-container">
                                <div class="rows-container">
                                    <div class="row" id="zona-1-2-3-4" >
                                        <div class="arch zona1">
                                            <!-- Generado dinámicamente por JS -->
                                        </div>
                                        <div class="arch zona2" >
                                            <!-- Generado dinámicamente por JS -->
                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="arch zona3">
                                            <!-- Generado dinámicamente por JS -->
                                        </div>
                                        <div class="arch zona4">
                                            <!-- Generado dinámicamente por JS -->
                                        </div>
                                    </div>
                                </div>      
                                <div class="rows-container" style="margin-top: 30px;">
                                    <div class="row" >
                                        <div class="arch zona5">
                                        
                                            <!-- Generado dinámicamente por JS -->
                                        </div>
                                        <div class="arch zona6">
                                            <!-- Generado dinámicamente por JS -->
                                        </div>                                    
                                    </div>                                
                                    <div class="row">                                   
                                        <div class="arch zona7">
                                            <!-- Generado dinámicamente por JS -->                                       
                                        </div>
                                        <div class="arch zona8">
                                            <!-- Generado dinámicamente por JS -->   
                                        </div>                                     
                                    </div>                                
                                </div>    
                            </div>
                        </div>
                        <!-- Referencias -->
                        <div class="col-lg-3">
                            <div class="card border border-dark" style=" font-size: 17px;">
                                <div class="card-body mx-1 px-0">
                                    <h6 class="card-title text-center mb-3 fw-bold" >REFERENCIAS</h6>
                                    <p class="mb-2 mx-0 px-0 " ><span class="text-danger fw-bold">ROJO:</span> Prestaciones Existentes</p>
                                    <p class="mb-2 mx-0 px-0"><span class="text-primary fw-bold">AZUL:</span> Prestaciones Requeridas</p>
                                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-danger">X</b>Diente Extraido</p>
                                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-primary">X</b>Diente a Extraer</p>
                                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-black">A</b>Diente Ausente</p>
                                    
                                    <p class="mb-2 mx-0 px-0"><span class="border border-danger border-2 me-1 align-middle" style="display: inline-block; width: 15px; height: 15px;"></span> Prótesis Fijas</p>
                                    <p class="mb-2 mx-0 px-0"><span class="border border-primary border-2 me-1 align-middle" style="display: inline-block; width: 15px; height: 15px;"></span> Prótesis Removible</p>
                                    <p class="mb-3 mx-0 px-0"><span class="border border-dark rounded-circle me-1 align-middle bg-warning " style="display: inline-block; width: 15px; height: 15px;"></span> Coronas</p>
                                    
                                    <div class="d-flex align-items-center " style="width: fit-content;">
                                        <label for="dientes" class="me-2 mb-0">
                                        <div class="d-block" style="line-height: 1;">
                                            <div>CANTIDAD DE</div>
                                            <div>DIENTES EXISTENTES</div>
                                        </div>
                                        </label>
                                        <input type="number" id="dientes" class="form-control form-control-sm border border-dark text-end" value="null" 
                                        style="width: 80px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
            <!-- ------------------ -->
            <div class="d-flex align-items-center gap-3 mb-3 mt-3">
                <span class="fw-semibold fs-5">Necesita Tratamiento Odontológico:</span>
                
                <div class="form-check form-check-inline d-flex align-items-center">
                    <input class="form-check-input me-1 border border-dark" type="radio" name="necesitaTratamiento" value="si" id="tratamiento-si">
                    <label class="form-check-label" for="tratamiento-si">SI</label>
                </div>

                <div class="form-check form-check-inline d-flex align-items-center">
                    <input class="form-check-input me-1 border border-dark" type="radio" name="necesitaTratamiento" value="no" id="tratamiento-no" checked>
                    <label class="form-check-label" for="tratamiento-no">NO</label>
                </div>
            </div>
            <!-- ----------- -->
            <div class="row mt-2">            
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="diagnostico" class="form-label fw-semibold me-2 fs-5" >Diagnóstico:</label>
                        <input type="text" class="form-control" id="diagnostico">
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="mb-1 d-flex">
                        <label for="tiempo" class="form-label fw-semibold fs-5">Tiempo (meses):</label>
                        <input type="number" class="form-control w-25 ms-2" id="tiempo">
                    </div>
                </div> 
            </div>
            <!-- Notas clínicas -->
            <div class="mt-2">
                <span class="fw-semibold fs-5">Observaciones:</span>
                <textarea class="form-control" id="clinical-notes" rows="4" placeholder="Introduzca las notas clínicas aquí..."></textarea>
            </div>        
            <!-- --------- -->
            <div class="mt-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-semibold fs-5">¿Está la persona en condiciones?:</span>
                    <div class="btn-group" role="group" aria-label="Apto selection">
                    <input type="radio" class="btn-check" name="apto" id="apto-si" value="si" autocomplete="off" checked>
                    <label class="btn btn-outline-success" for="apto-si">
                        <i class="bi bi-check-circle me-1"></i> Apto
                    </label>
                    <input type="radio" class="btn-check" name="apto" id="apto-no" value="no" autocomplete="off">
                    <label class="btn btn-outline-danger" for="apto-no">
                        <i class="bi bi-x-circle me-1"></i> No Apto
                    </label>
                    </div>
                </div>
            </div>
            <!-- Botones de exportación -->
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button class="btn btn-primary" id="export-image">
                    Generar <i class="bi bi-image me-2"></i> 
                </button>
                <button class="btn btn-danger" id="export-pdf">
                    Exportar <i class="bi bi-file-earmark-pdf me-2"></i> 
                </button>
            </div>
            <!-- Vista previa -->
            <div id="preview-container" class="mt-4 text-center d-none">
                <h3>Vista previa</h3>
                <div class="border p-2 mb-3">
                    <img id="preview-image" class="img-fluid" alt="Vista previa del odontograma">
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <a id="download-link" class="btn btn-primary" href="#" download="odontograma.png">Descargar Imagen</a>
                    <a id="download-pdf" class="btn btn-danger d-none" href="#" download="odontograma.pdf">Descargar PDF</a>                
                </div>
            </div>
        </div>    
        <!-- Canvas oculto para la exportación -->
        <canvas id="export-canvas"></canvas>
        
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del odontograma
        const zona11 = [18, 17, 16, 15, 14, 13, 12, 11]; // Cuadrante 1
        const zona22 = [21, 22, 23, 24, 25, 26, 27, 28];  // Cuadrante 2
        const zona33 = [48, 47, 46, 45, 44, 43, 42, 41]; // Cuadrante 4
        const zona44 = [31, 32, 33, 34, 35, 36, 37, 38];  // Cuadrante 3
        const zona55 = [55, 54, 53, 52, 51];  // Cuadrante 5
        const zona66 = [61, 62, 63, 64, 65];  // Cuadrante 6
        const zona77 = [85, 84, 83, 82, 81];  // Cuadrante 8
        const zona88 = [71, 72, 73, 74, 75];  // Cuadrante 7       
        let selectedTreatment = null;
        const toothData = {};
        
        // Definir la geometría de las partes del diente (usando SVG)
        const toothPartGeometry = {
            top: 'points="0,0 100,0 75,25 25,25"',
            right: 'points="100,0 100,100 75,75 75,25"',
            bottom: 'points="100,100 0,100 25,75 75,75"',
            left: 'points="0,100 0,0 25,25 25,75"',
            center: 'x="25" y="25" width="50" height="50"'
        };
        
        // Crear el odontograma
        function createOdontogram() {
            createArch('zona1', zona11);
            createArch('zona2', zona22);
            createArch('zona3', zona33);                
            createArch('zona4', zona44);
            createArch('zona5', zona55);
            createArch('zona6', zona66);
            createArch('zona7', zona77);                
            createArch('zona8', zona88,);                
            // Establecer fecha actual en el campo de fecha
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('exam-date').value = today;
        }
        
        // Crear arco dental
        function createArch(archClass, teethNumbers) {
            const archElement = document.querySelector(`.${archClass}`);                
            // Verificar si es una zona inferior
            const isLowerArch = archClass === 'zona3' || archClass === 'zona4' || archClass === 'zona7' || archClass === 'zona8';
            // Añadir la etiqueta "Izquierda" al final de la zona 5
            if (archClass === 'zona5' || archClass === 'zona7') {
                const labelDiv = document.createElement('div');
                labelDiv.className = 'tooth-label-izquierda';
                labelDiv.style.fontWeight = 'bold';
                labelDiv.style.display = 'flex';
                labelDiv.style.alignItems = 'center';
                labelDiv.style.paddingRight = '20px';
                
                labelDiv.textContent = 'Izquierda';
                if (archClass === 'zona7') {
                    labelDiv.style.paddingBottom = '25px';
                } else {
                    labelDiv.style.color = 'transparent'; // El texto está, pero no se ve
                }
                archElement.appendChild(labelDiv);
            }
            teethNumbers.forEach(toothNumber => {
                const toothContainer = document.createElement('div');
                toothContainer.className = 'tooth-container';
                
                // Crear el diente con SVG
                const tooth = document.createElement('div');
                tooth.className = 'tooth';
                tooth.dataset.number = toothNumber;
                
                // Número de diente
                const toothNumberEl = document.createElement('div');
                toothNumberEl.className = 'tooth-number';
                toothNumberEl.textContent = toothNumber;
                
                // Colocar el número según la zona
                if (!isLowerArch) {
                    toothContainer.appendChild(toothNumberEl); // Primero el número
                }                    
                // Crear el SVG
                const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                svg.setAttribute("viewBox", "0 0 100 100");
                
                // Crear las partes del diente usando SVG
                for (const [partName, geometry] of Object.entries(toothPartGeometry)) {
                    let part;
                    if (partName === 'center') {
                        part = document.createElementNS("http://www.w3.org/2000/svg", "rect");
                        const attrs = geometry.split(/\s+/);
                        for (const attr of attrs) {
                            if (attr.includes('=')) {
                                const [name, value] = attr.replace(/"/g, '').split('=');
                                part.setAttribute(name, value);
                            }
                        }
                    } else {
                        part = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
                        part.setAttribute("points", geometry.split('="')[1].replace('"', ''));
                    }
                    
                    part.classList.add("tooth-part");
                    part.dataset.part = partName;
                    part.dataset.tooth = toothNumber;
                    
                    // Añadir evento de clic
                    part.addEventListener('click', function() {
                        if (selectedTreatment) {
                            handleToothPartClick(toothNumber, partName);
                        }
                    });
                    
                    svg.appendChild(part);
                }
                
                // Crear la marca X para diente ausente
                const xMark = document.createElementNS("http://www.w3.org/2000/svg", "text");
                xMark.setAttribute("x", "50");
                xMark.setAttribute("y", "80");
                xMark.setAttribute("text-anchor", "middle");
                xMark.setAttribute("font-size", "100");
                xMark.setAttribute("fill", "black");                    
                xMark.classList.add("x-mark");
                xMark.textContent = "A";
                svg.appendChild(xMark);
                
                tooth.appendChild(svg);
                toothContainer.appendChild(tooth);
                
                // Para zonas inferiores, el número va después del diente
                if (isLowerArch) {
                    toothContainer.appendChild(toothNumberEl);
                }
                
                archElement.appendChild(toothContainer);
                // Inicializar datos del diente
                toothData[toothNumber] = {
                    top: null,
                    right: null,
                    bottom: null,
                    left: null,
                    center: null,
                    isAbsent: false
                };
                
            });
            // Añadir la etiqueta "Izquierda" al final de la zona 8 (después de todos los dientes)
            if (archClass === 'zona8' || archClass === 'zona6') {
                const labelDiv = document.createElement('div');
                labelDiv.className = 'tooth-label-izquierda';
                labelDiv.style.fontWeight = 'bold';
                labelDiv.style.display = 'flex';
                labelDiv.style.alignItems = 'center';                
                labelDiv.textContent = 'Derecha';
                if (archClass === 'zona8') {
                    labelDiv.style.marginLeft = '10px';
                    labelDiv.style.paddingLeft = '10px';
                    labelDiv.style.paddingBottom = '25px';
                } else {
                    labelDiv.style.marginRight = '10px';
                    labelDiv.style.color = 'transparent'; // El texto está, pero no se ve
                }
                archElement.appendChild(labelDiv);
            }
        }
        
        // Manejar la selección de tratamiento
        document.querySelectorAll('.control-btn').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.control-btn').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedTreatment = this.dataset.treatment;
                });
        });
        
        // Manejar clic en parte del diente
        function handleToothPartClick(toothNumber, partName) {
                const tooth = document.querySelector(`.tooth[data-number="${toothNumber}"]`);
                const toothPart = document.querySelector(`.tooth-part[data-tooth="${toothNumber}"][data-part="${partName}"]`);
                const svg = tooth.querySelector('svg');
                const xMark = svg.querySelector('.x-mark');
                
                if (selectedTreatment === 'limpiar') {
                    // Limpiar
                    if (toothData[toothNumber].isAbsent) {
                        tooth.classList.remove('tooth-ausente');
                        toothData[toothNumber].isAbsent = false;
                    }
                    
                    // Restaurar parte a estado original
                    toothPart.classList.remove('existente', 'requerida', 'ausente', 'corona');
                    toothData[toothNumber][partName] = null;
                    
                    // Resetear marcas X y bordes
                    xMark.textContent = "A";
                    xMark.setAttribute("fill", "black");
                    xMark.style.display = "none";
                    
                    // Resetear bordes para prótesis
                    tooth.querySelectorAll('.tooth-part').forEach(part => {
                        part.style.stroke = "black";
                        part.style.strokeWidth = "1";
                    });
                    
                } else if (selectedTreatment === 'ausente') {
                    // Marcar diente como ausente
                    tooth.classList.add('tooth-ausente');
                    toothData[toothNumber].isAbsent = true;
                    
                    // Mostrar la X con "A" negra
                    xMark.textContent = "A";
                    xMark.setAttribute("fill", "black");
                    xMark.style.display = "block";
                    
                    // Marcar todas las partes como ausentes
                    tooth.querySelectorAll('.tooth-part').forEach(part => {
                        part.classList.remove('existente', 'requerida', 'corona');
                        part.classList.add('ausente');
                        const partDataName = part.dataset.part;
                        toothData[toothNumber][partDataName] = 'ausente';
                    });
                    
                } else if (selectedTreatment === 'extraido') {
                    // Marcar diente como extraído
                    tooth.classList.add('tooth-ausente');
                    toothData[toothNumber].isAbsent = true;
                    
                    // Mostrar la X con color rojo
                    xMark.textContent = "X";
                    xMark.setAttribute("fill", "#FF0000"); // Rojo
                    xMark.style.display = "block";
                    
                    // Marcar todas las partes como ausentes
                    tooth.querySelectorAll('.tooth-part').forEach(part => {
                        part.classList.remove('existente', 'requerida', 'corona');
                        part.classList.add('ausente');
                        const partDataName = part.dataset.part;
                        toothData[toothNumber][partDataName] = 'extraido';
                    });
                    
                } else if (selectedTreatment === 'extraer') {
                    // Marcar diente para extraer
                    tooth.classList.add('tooth-ausente');
                    toothData[toothNumber].isAbsent = true;                    
                    // Mostrar la X con color azul
                    xMark.textContent = "X";
                    xMark.setAttribute("fill", "#0000FF"); // Azul
                    xMark.style.display = "block";                    
                    // Marcar todas las partes como ausentes
                    tooth.querySelectorAll('.tooth-part').forEach(part => {
                        part.classList.remove('existente', 'requerida', 'corona');
                        part.classList.add('ausente');
                        const partDataName = part.dataset.part;
                        toothData[toothNumber][partDataName] = 'extraer';
                    });                    
                } else if (selectedTreatment === 'fija') {
                        // Marcar diente con prótesis removible (borde azul)
                        if (toothData[toothNumber].isAbsent) {
                            tooth.classList.remove('tooth-ausente');
                            toothData[toothNumber].isAbsent = false;
                        }                        
                        
                        // Aplicar borde grueso azul a todas las partes
                        tooth.querySelectorAll('.tooth-part').forEach(part => {
                            part.style.stroke = "red"; // Azul
                            part.style.strokeWidth = "5";
                            const partDataName = part.dataset.part;
                            toothData[toothNumber][partDataName] = 'fija';
                        });                        
                        // Ocultar la X si estaba visible
                        xMark.style.display = "none";
                } else if (selectedTreatment === 'removible') {
                        // Marcar diente con prótesis removible (borde azul)
                        if (toothData[toothNumber].isAbsent) {
                            tooth.classList.remove('tooth-ausente');
                            toothData[toothNumber].isAbsent = false;
                        }                        
                        // Aplicar borde grueso azul a todas las partes
                        tooth.querySelectorAll('.tooth-part').forEach(part => {
                            part.style.stroke = "#0000FF"; // Azul
                            part.style.strokeWidth = "5";
                            const partDataName = part.dataset.part;
                            toothData[toothNumber][partDataName] = 'removible';
                        });                        
                        // Ocultar la X si estaba visible
                        xMark.style.display = "none";
                } else {
                    // Aplicar otros tratamientos específicos (existente, requerida, corona, etc.)
                    if (toothData[toothNumber].isAbsent) {
                        tooth.classList.remove('tooth-ausente');
                        toothData[toothNumber].isAbsent = false;
                        xMark.style.display = "none";
                    }                    
                    // Remover cualquier clase anterior y añadir la nueva
                    toothPart.classList.remove('existente', 'requerida', 'ausente', 'corona');
                    toothPart.classList.add(selectedTreatment);
                    toothData[toothNumber][partName] = selectedTreatment;
                }
        }            
        // Crear el contenido para la exportación
        function createExportContent() {
            const patientName = document.getElementById('patient-name').value || 'Paciente';
            const birthDate = document.getElementById('birth-date').value || '';
            const examDate = document.getElementById('exam-date').value || '';
            const notes = document.getElementById('clinical-notes').value;
            // Agregar los campos que faltan
            const telefono = document.getElementById('tel').value || '';
            const domicilio = document.getElementById('domicilio').value || '';
            const localidad = document.getElementById('localidad').value || '';
            const tratamientoRadio = document.querySelector('input[name="necesitaTratamiento"]:checked');
            const necesitaTratamiento = tratamientoRadio ? tratamientoRadio.value : 'no';

            const diagnostico = document.getElementById('diagnostico').value || '';
            const tiempo = document.getElementById('tiempo').value || '';

            const aptonoapto = document.querySelector('input[name="apto"]:checked');
            const apto = aptonoapto ? aptonoapto.value : 'no';
            
            // Crear div temporal para la captura
            const tempDiv = document.createElement('div');            
            tempDiv.style.backgroundColor = 'white';
            tempDiv.style.width = '1100px';
            tempDiv.style.position = 'absolute';
            tempDiv.style.left = '-9999px';
            tempDiv.style.fontFamily = 'Arial, sans-serif';
            tempDiv.style.paddingLeft = '10px';
            tempDiv.style.paddingRight = '10px';            
            // Información del paciente
            const headerDiv = document.createElement('div');
            headerDiv.style.marginBottom = '0px';
            headerDiv.style.paddingBottom = '0px';
            headerDiv.innerHTML = `
                <h2 style="text-align: center;">Odontograma - Fecha de Examen: ${formatDate(examDate)}</h2>                    
                <p><strong>Nombre y Apellido:</strong> ${patientName ? patientName : ''}</p>
                <p>
                    ${domicilio ? `<strong>Fecha de Nacimiento:</strong> <span style="margin-right: 80px;">${birthDate ? formatDate(birthDate) : ''}</span>` : ''}
                    ${telefono ? `<strong>Teléfono:</strong> <span>${telefono}</span>` : ''}
                </p>
                <p>
                    ${domicilio ? `<strong>Domicilio:</strong> <span style="margin-right: 80px;">${domicilio}</span>` : ''}                    
                    ${localidad ? `<strong>Localidad:</strong> <span style="margin-right: 80px;">${localidad}</span>` : ''}                    
                </p>
            `;
            tempDiv.appendChild(headerDiv);
            // Crear línea divisoria
            const hrDiv1 = document.createElement('div');
            hrDiv1.style.margin = '0px 0';
            hrDiv1.style.border = '1px solid black';
            tempDiv.appendChild(hrDiv1);
            
            // Crear una tabla para el layout
            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            
            const tr = document.createElement('tr');
            
            // Celda para el odontograma (70%)
            const tdOdontograma = document.createElement('td');
            tdOdontograma.style.width = '60%';
            tdOdontograma.style.verticalAlign = 'top';
            
            // Crear un contenedor para el odontograma con estilo específico para la exportación
            const odontogramaExport = document.createElement('div');
            odontogramaExport.className = 'odontograma-export';
            odontogramaExport.style.display = 'flex';
            odontogramaExport.style.flexDirection = 'column';
            odontogramaExport.style.alignItems = 'center';
            odontogramaExport.style.width = '100%';
            odontogramaExport.style.margin = '20px 0';
            // Primera fila: Zonas 1-2 (adultos superiores)
            const row1 = document.createElement('div');
            row1.style.display = 'flex';
            row1.style.width = '100%';
            row1.style.justifyContent = 'center';
            row1.style.flexWrap = 'nowrap'; // Forzar que no se envuelvan                
            
            // Clonar zonas 1 y 2
            const zona1Clone = document.querySelector('.zona1').cloneNode(true);
            zona1Clone.style.display = 'flex';
            zona1Clone.style.flexWrap = 'nowrap';
            
            const zona2Clone = document.querySelector('.zona2').cloneNode(true);
            zona2Clone.style.display = 'flex';
            zona2Clone.style.flexWrap = 'nowrap';
            
            // Agregar línea vertical divisoria
            zona2Clone.style.borderLeft = '2px solid black';
            
            row1.appendChild(zona1Clone);
            row1.appendChild(zona2Clone);
            
            // Segunda fila: Zonas 3-4 (adultos inferiores)
            const row2 = document.createElement('div');
            row2.style.display = 'flex';
            row2.style.width = '100%';
            row2.style.justifyContent = 'center';
            row2.style.flexWrap = 'nowrap'; // Forzar que no se envuelvan
            row2.style.borderTop = '2px solid black'; // Línea horizontal divisoria
            
            // Clonar zonas 3 y 4
            const zona3Clone = document.querySelector('.zona3').cloneNode(true);
            zona3Clone.style.display = 'flex';
            zona3Clone.style.flexWrap = 'nowrap';
            
            const zona4Clone = document.querySelector('.zona4').cloneNode(true);
            zona4Clone.style.display = 'flex';
            zona4Clone.style.flexWrap = 'nowrap';
            
            // Agregar línea vertical divisoria
            zona4Clone.style.borderLeft = '2px solid black';
            
            row2.appendChild(zona3Clone);
            row2.appendChild(zona4Clone);
            
            // Espacio entre adultos y temporales
            const separator = document.createElement('div');
            separator.style.height = '30px';
            
            // Tercera fila: Zonas 5-6 (temporales superiores)
            const row3 = document.createElement('div');
            row3.style.display = 'flex';
            row3.style.width = '65%';
            row3.style.justifyContent = 'center';
            row3.style.flexWrap = 'nowrap';
            row3.style.marginRight = '15px';
            
            // Clonar zonas 5 y 6
            const zona5Clone = document.querySelector('.zona5').cloneNode(true);
            zona5Clone.style.display = 'flex';
            zona5Clone.style.flexWrap = 'nowrap';
            
            const zona6Clone = document.querySelector('.zona6').cloneNode(true);
            zona6Clone.style.display = 'flex';
            zona6Clone.style.flexWrap = 'nowrap';
            
            // Agregar línea vertical divisoria
            zona6Clone.style.borderLeft = '2px solid black';
            
            row3.appendChild(zona5Clone);
            row3.appendChild(zona6Clone);
            
            // Cuarta fila: Zonas 7-8 (temporales inferiores)
            const row4 = document.createElement('div');
            row4.style.display = 'flex';
            row4.style.width = '63%';
            row4.style.justifyContent = 'center';
            row4.style.flexWrap = 'nowrap';
            row4.style.borderTop = '2px solid black'; // Línea horizontal divisoria
            row4.style.marginRight = '0px';
            // Clonar zonas 7 y 8
            const zona7Clone = document.querySelector('.zona7').cloneNode(true);
            zona7Clone.style.display = 'flex';
            zona7Clone.style.flexWrap = 'nowrap';
            zona7Clone.style.marginLeft = '5px';
            zona7Clone.style.paddingRight = '5px';
            zona7Clone.style.marginRight = '0px';       
            const zona8Clone = document.querySelector('.zona8').cloneNode(true);
            zona8Clone.style.display = 'flex';
            zona8Clone.style.flexWrap = 'nowrap';
            zona8Clone.style.paddingRight = '15px';
            // Agregar línea vertical divisoria
            zona8Clone.style.borderLeft = '2px solid black';
            // Añadir todos los elementos a la fila
            row4.appendChild(zona7Clone);
            row4.appendChild(zona8Clone);
            
            // Componer el odontograma
            odontogramaExport.appendChild(row1);
            odontogramaExport.appendChild(row2);
            odontogramaExport.appendChild(separator);
            odontogramaExport.appendChild(row3);
            odontogramaExport.appendChild(row4);         
            tdOdontograma.appendChild(odontogramaExport);
            
            // Celda para referencias (30%)
            const tdReferencias = document.createElement('td');
            tdReferencias.style.width = '100%';
            tdReferencias.style.verticalAlign = 'top';
            tdReferencias.style.paddingLeft = '10px';
            tdReferencias.style.margin = '0px 0px';            
            // Agregar el cuadro de referencias como HTML
            tdReferencias.innerHTML = `
            <div class="card border border-dark mt-4" style=" font-size: 15px;">
                <div class="card-body mx-1 px-0">
                    <h6 class="card-title text-center mb-3 fw-bold" >REFERENCIAS</h6>
                    <p class="mb-2 mx-0 px-0 " ><span class="text-danger fw-bold">ROJO:</span> Prestaciones Existentes</p>
                    <p class="mb-2 mx-0 px-0"><span class="text-primary fw-bold">AZUL:</span> Prestaciones Requeridas</p>
                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-danger">X</b>Diente Extraido</p>
                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-primary">X</b>Diente a Extraer</p>
                    <p class="mb-2 mx-0 px-0"><b class="fs-6 me-2 text-black">A</b>Diente Ausente</p>
                    
                    <p class="mb-2 mx-0 px-0"><span class="border border-danger border-2 me-1 align-middle" style="display: inline-block; width: 15px; height: 15px;"></span> Prótesis Fijas</p>
                    <p class="mb-2 mx-0 px-0"><span class="border border-primary border-2 me-1 align-middle" style="display: inline-block; width: 15px; height: 15px;"></span> Prótesis Removible</p>
                    <p class="mb-3 mx-0 px-0"><span class="border border-dark rounded-circle me-1 align-middle bg-warning " style="display: inline-block; width: 15px; height: 15px;"></span> Coronas</p>
                    
                    <div class="d-flex align-items-center " style="width: fit-content;">
                        <label for="dientes" class="me-2 mb-0">
                        <div class="d-block" style="line-height: 1;">
                            <div>CANTIDAD DE</div>
                            <div>DIENTES EXISTENTES</div>
                        </div>
                        </label>
                        <input type="number" id="dientes" class="form-control form-control-sm border border-dark text-end" value="${document.getElementById('dientes').value || ''}" 
                        style="width: 80px;">
                    </div>
                </div>
            </div>
            `;     
            // Agregar celdas a la fila
            tr.appendChild(tdOdontograma);
            tr.appendChild(tdReferencias);            
            // Agregar fila a la tabla
            table.appendChild(tr);            
            // Agregar tabla al contenedor principal
            tempDiv.appendChild(table);
            // Crear línea divisoria
            const hrDiv = document.createElement('div');
            hrDiv.style.margin = '0px 0';
            hrDiv.style.border = '1px solid black';
            tempDiv.appendChild(hrDiv);
            // ----------------
            const trataDiv = document.createElement('div');
                trataDiv.style.marginTop = '10px';
                trataDiv.innerHTML = `                    
                    <span style="display: inline-block; font-size: 16px;">Necesita Tratamiento Odontológico:</span>
                    <div style="display: inline-block; font-size: 16px; margin-left: 5px; font-weight:bold;">
                        ${necesitaTratamiento === 'si' ? 'SI' : 'NO'}
                    </div>                      
                `;
                tempDiv.appendChild(trataDiv);    
            // -------Diagnóstico y tiempo
            if (diagnostico.trim() || tiempo.trim()) {
                const diagDiv = document.createElement('div');
                diagDiv.style.marginTop = '10px';
                diagDiv.innerHTML = `
                    <div style="display: flex; gap: 20px;">
                        ${diagnostico ? `<span><strong>Diagnóstico:</strong> ${diagnostico}</span>` : ''}
                        ${tiempo ? `<span><strong>Tiempo (meses):</strong> ${tiempo}</span>` : ''}
                    </div>
                `;
                tempDiv.appendChild(diagDiv);
            }
            // ------Añadir notas clínicas
            if (notes.trim()) {
                const notesDiv = document.createElement('div');
                notesDiv.style.marginTop = '10px';
                notesDiv.innerHTML = `
                    <div class="row"> 
                        <div class="col-auto"> 
                            <h6><strong>Observaciones:</strong></h6>
                        </div>
                        <div class="col"> 
                            <p>${notes.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                `;
                tempDiv.appendChild(notesDiv);
            }                
            // ----------Agregar estado apto/no apto
            const aptoDiv = document.createElement('div');
            aptoDiv.style.marginTop = '0px';
            aptoDiv.innerHTML = `
                <span style="display: inline-block; font-size: 16px;">¿Persona en condiciones?:</span>
                <div style="display: inline-block; font-size: 16px; margin-left: 5px; font-weight:bold;">
                    ${apto === 'si' ? 'APTO' : 'NO APTO'}
                </div>
            `;
            tempDiv.appendChild(aptoDiv); 
            // ----------  
            return { tempDiv, patientName };
        }
        
        // Función para formatear fechas
        function formatDate(dateString) {
                if (!dateString) return '';                
                // Separar la fecha en partes
                const parts = dateString.split('-');
                if (parts.length !== 3) return dateString;                
                // Crear la fecha usando componentes específicos para evitar problemas de zona horaria
                const year = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1; // En JavaScript los meses van de 0-11
                const day = parseInt(parts[2]);                
                // Crear objeto Date con la fecha local específica
                const date = new Date(year, month, day);                
                // Formatear la fecha en formato español
                return date.toLocaleDateString('es-ES');
        }
        
        // Exportar el odontograma como imagen
        document.getElementById('export-image').addEventListener('click', function() {
                const { tempDiv, patientName } = createExportContent();
                
                document.body.appendChild(tempDiv);
                
                html2canvas(tempDiv, {
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                }).then(canvas => {
                    document.body.removeChild(tempDiv);
                    
                    const imageDataURL = canvas.toDataURL('image/png');
                    
                    // Mostrar la vista previa
                    const previewContainer = document.getElementById('preview-container');
                    const previewImage = document.getElementById('preview-image');
                    previewImage.src = imageDataURL;
                    
                    // Configurar enlace de descarga PNG
                    const downloadLink = document.getElementById('download-link');
                    downloadLink.href = imageDataURL;
                    downloadLink.download = `odontograma_${patientName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,10)}.png`;
                    
                    // Guardar el canvas para PDF
                    window.exportCanvas = canvas;
                    
                    // Configurar enlace de descarga PDF
                    const downloadPDF = document.getElementById('download-pdf');
                    downloadPDF.addEventListener('click', function(e) {
                        e.preventDefault();
                        exportToPDF(canvas, patientName);
                    });
                    
                    // Mostrar contenedor de vista previa
                    previewContainer.classList.remove('d-none');
                    
                    // Desplazarse a la vista previa
                    previewContainer.scrollIntoView({ behavior: 'smooth' });
                });
        });
        
        // Exportar directamente a PDF
        document.getElementById('export-pdf').addEventListener('click', function() {
                const { tempDiv, patientName } = createExportContent();
                
                document.body.appendChild(tempDiv);
                
                html2canvas(tempDiv, {
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                }).then(canvas => {
                    document.body.removeChild(tempDiv);
                    exportToPDF(canvas, patientName);
                });
        });
        
        // Función para exportar a PDF
        function exportToPDF(canvas, patientName) {
                const { jsPDF } = window.jspdf;                
                // Crear el PDF
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                });
                
                // Obtener dimensiones del canvas y ajustar para el PDF
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 280;  // Ancho en mm para A4 landscape (menos margen)
                const imgHeight = canvas.height * imgWidth / canvas.width;
                
                // Agregar imagen al PDF
                pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
                
                // Guardar PDF
                const filename = `odontograma_${patientName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0,10)}.pdf`;
                pdf.save(filename);
        }
        
        // Calcular número de dientes existentes
        function updateTeethCount() {
                let existingTeeth = 0;
                
                // Contar dientes no ausentes
                for (const toothNumber in toothData) {
                    if (!toothData[toothNumber].isAbsent) {
                        existingTeeth++;
                    }
                }
                
                // Actualizar referencia
                document.querySelector('.valor-cantidad').textContent = existingTeeth;
        }
        
        // Observador para actualizar conteo de dientes
        const odontogramaObserver = new MutationObserver(function(mutations) {
            updateTeethCount();
        });
        
        // Iniciar observador
        const odontogramaContainer = document.getElementById('odontograma-container');
        odontogramaObserver.observe(odontogramaContainer, { 
            attributes: true, 
            childList: true, 
            subtree: true 
        });
        createOdontogram();
        updateTeethCount();
    });
</script>
</body>
</html>
