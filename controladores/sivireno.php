<?php

use Google\Service\CloudControlsPartnerService\Console;

session_start();

require_once(__DIR__ . '/../modelos/Sivireno.php');

$sivireno = new Sivireno();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'verificarTramite':
    if (!isset($_SESSION)) { session_start(); }
    $id_estu = $_SESSION['sistema_academico']['id_estu'];
    
    $res = ["status" => false, "mensaje" => ""];

    if (isset($_POST['id_tupa'])) {
        $id_tupa = $_POST['id_tupa'];

        switch ($id_tupa) {
            case '1': // Certificado de Estudios
                $asignaturas_aprobadas = $sivireno->numAsigAprobadas($id_estu);
                $total_aprobadas = (isset($asignaturas_aprobadas['total_aprobados'])) ? $asignaturas_aprobadas['total_aprobados'] : 0;
                 if ($total_aprobadas >= 1) {
                    $res['status'] = true;
                    $res['mensaje'] = "Cumples con los requisitos minimos. Apto para solicitar el Certificado de Estudios.";
                } else {
                    $res['status'] = false;
                    $res['bloqueo'] = true; // Bloqueo duro
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Certificado de Estudios.";
                }
            break;
            case '2': // Constancia de Matrícula
                $actual = $sivireno->semestreDatos($id_estu);   
                if ($actual && !empty($actual['id_semestre'])) {
                    $res['status'] = true;
                    $res['mensaje'] = "Estás matriculado en el semestre: " . $actual['nomsemestre'] ;

                }else {
                    $res['status'] = false;
                    $res['bloqueo'] = true;
                    $res['mensaje'] = "El estudiante no se encuentra matriculado en el semestre actual.";
                }
    
            break;
            case '3': //  Ampliación de Créditos
                $actual = $sivireno->semestreDatos($id_estu); 
                
                if ($actual && !empty($actual['id_semestre'])) {
                    $id_semestre = $actual['id_semestre'];
                    $id_plan = $actual['id_plan'];

                    $res_anterior = $sivireno->oldSemestre($id_estu); 
                    
                    if ($reg_ant = $res_anterior->fetch_object()) {
                        $id_sem_anterior = $reg_ant->id_semestre;
                        $res_promedio = $sivireno->calcularPromedio($id_estu, $id_sem_anterior);
                        
                        $promedio = (isset($res_promedio['promedio'])) ? $res_promedio['promedio'] : 0;

                        if ($promedio >= 14) {
                            $res_creditos = $sivireno->obtenerCreditosPermitidos($id_estu, $id_semestre, $id_plan);
                            $max_creditos = (isset($res_creditos['credito'])) ? $res_creditos['credito'] : "No definido";
                            if ($actual['ciclo_ficham'] === 'X') {
                                $creditos = 30;
                            }else {
                                $creditos = 27;
                            }
                            
                            $res['status'] = true;
                            $res['mensaje'] = "TU Promedio es: " . number_format($promedio, 2) . ". Tienes $max_creditos créditos en el ciclo actual. Apto para llevar hasta $creditos créditos.";
                        } else {
                            $res['status'] = false;
                            $res['bloqueo'] = false;
                            $res['mensaje'] = "Tu promedio es " . number_format($promedio, 2) . ". No cumples con el mínimo de 14.";
                        }
                    } else {
                        $res['mensaje'] = "No se encontró historial académico previo para validar.";
                    }
                } else {
                    $res['status'] = false;
                    $res['bloqueo'] = true;
                    $res['mensaje'] = "El estudiante no se encuentra matriculado en el semestre actual.";
                }
                break;
            case '4': //  Constancia de Estudios
                $asignaturas_aprobadas = $sivireno->numAsigAprobadas($id_estu);
                $total_aprobadas = (isset($asignaturas_aprobadas['total_aprobados'])) ? $asignaturas_aprobadas['total_aprobados'] : 0;
                 if ($total_aprobadas >= 1) {
                    $res['status'] = true;
                    $res['mensaje'] = "Cumples con los requisitos minimos. Apto para solicitar el Constancia de Estudios.";
                } else {
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Constancia de Estudios.";
                }
            break;
             case '5': 
                $ordenes = [
                    '0' => 'Ninguno',
                    '1' => 'Primer Puesto',
                    '2' => 'Segundo Puesto',
                    '3' => 'Tercer Puesto',
                    '4' => 'Décimo Superior',
                    '5' => 'Quinto Superior',
                    '6' => 'Tercio Superior',
                    '7' => 'Medio Superior'
                ];

                $semestre_info = $sivireno->obtenerSemestreCulminado($id_estu);

                if ($semestre_info && isset($semestre_info['id_semestre'])) {
                    $id_sem_target = $semestre_info['id_semestre'];
                    $nom_sem_estu = $semestre_info['nomsemestre'];
                    
                    $orden_merito = $sivireno->obtenerOrdenMerito($id_estu, $id_sem_target);
                    
                    // Obtenemos el código numérico del OM que viene de la ficha de matricula
                    $codigo_om = isset($orden_merito['orden de merito']) ? $orden_merito['orden de merito'] : null;

                    // Caso A: Tiene un mérito (Códigos 1 al 7)
                    if ($codigo_om !== null && $codigo_om > 0) {
                        $res['status'] = true;
                        $texto_merito = $ordenes[$codigo_om];
                        $res['mensaje'] = "Usted pertenece al **" . $texto_merito . "** en el semestre **" . $nom_sem_estu . "**. Puede proceder con su solicitud de este ciclo o periodos anteriores (el ciclo actual en curso no cuenta con ranking oficial).";
                    } 
                    // Caso B: El código es 0 (Procesado, pero no alcanzó puesto)
                    else if ($codigo_om === '0' || $codigo_om === 0) {
                        $res['status'] = false;
                        $res['mensaje'] = "Para el semestre " . $nom_sem_estu . ", usted no registra una posición de mérito superior (Tercio, Quinto, etc.). Verifique si cumplió con los requisitos mínimos de créditos y notas aprobadas.";
                    }
                    // Caso C: Es NULL (Duda: ¿En proceso o no aplica?)
                    else {
                        $res['status'] = false;
                        $res['mensaje'] = "No se puede determinar su orden de mérito para el semestre " . $nom_sem_estu . ". Esto puede deberse a que el ranking está en proceso de cálculo o a que su situación académica no aplica para el cuadro de méritos.";
                    }

                } else {
                    // Estudiantes de primer ciclo o sin historial regular
                    $res['status'] = false;
                    $res['bloqueo'] = true; // Bloqueo duro
                    $res['mensaje'] = "No cuenta con historial de semestres regulares cerrados. El trámite no está disponible para alumnos de primer ciclo, ya que se requiere al menos un periodo culminado.";
                }
                break;
            case '6': //  CONSTANCIA DE EGRESADO
                $egresado = $sivireno->ObtenerEgresado($id_estu);
                if ($egresado) {
                    $res['status'] = true;
                    $res['mensaje'] = "Requisitos cumplidos. Puede proceder con su solicitud de Constancia de Egresado.";
                } else {
                    $res['status'] = false;
                    $res['mensaje'] = "No cumple con los requisitos para solicitar la Constancia de Egresado.";
                    $res['bloqueo'] = true;  
                }
            break;
            
            case '7': //  Record Integral de Notas
                $asignaturas_aprobadas = $sivireno->numAsigAprobadas($id_estu);
                $total_aprobadas = (isset($asignaturas_aprobadas['total_aprobados'])) ? $asignaturas_aprobadas['total_aprobados'] : 0;
                 if ($total_aprobadas >= 1) {
                    $res['status'] = true;
                    $res['mensaje'] = "Cumples con los requisitos minimos. Apto para solicitar el Record Integral de Notas.";
                } else {
                    $res['status'] = false; 
                    $res['bloqueo'] = true;
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Record Integral de Notas.";
                }
            break;
            case '8': // solicitud de certificado de practicas pre profesionales
                 $asignaturas_aprobadas = $sivireno->numAsigAprobadas($id_estu);
                $certificadoPracticas= $sivireno->numAsigAprobadas($id_estu);
                $total_aprobadas = (isset($asignaturas_aprobadas['total_aprobados'])) ? $asignaturas_aprobadas['total_aprobados'] : 0;
                 if ($total_aprobadas >= 1) {
                    $res['status'] = true;
                    $res['mensaje'] = "Cumples con los requisitos minimos. Apto para solicitar el Record Integral de Notas.";
                } else {
                    $res['status'] = false; 
                    $res['bloqueo'] = true;
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Record Integral de Notas.";
                }
            break;
            case '9': // Solicitud de Carta de Prácticas Pre Profesionales
                // 1. Obtenemos el semestre actual
                $datos_actuales = $sivireno->semestreDatos($id_estu);

                // 2. Validamos que la consulta del semestre haya devuelto datos
                if (is_array($datos_actuales) && isset($datos_actuales['id_semestre'])) {
                    $id_semestre_actual = $datos_actuales['id_semestre'];

                    // 3. Ejecutamos nuestra validación en la BD
                    $certificadoPracticas = $sivireno->verificarPractica($id_estu, $id_semestre_actual);

                    // 4. Extraemos el total de la consulta (validando que sea un arreglo y exista el índice)
                    $total_matriculado = (is_array($certificadoPracticas) && isset($certificadoPracticas['total_practicas'])) ? $certificadoPracticas['total_practicas'] : 0;

                    // 5. Armamos la respuesta lógica
                    if ($total_matriculado >= 1) {
                        $res['status'] = true;
                        $res['mensaje'] = "Cumples con los requisitos mínimos. Apto para solicitar la Carta de Presentación de Prácticas Preprofesionales.";
                    } else {
                        $res['status'] = false; 
                        $res['bloqueo'] = true;
                        $res['mensaje'] = "No cumples con los requisitos. Debes estar matriculado en el curso de Prácticas Preprofesionales en el semestre actual para solicitar esta carta.";
                    }
                } else {
                    // Si no hay datos del semestre actual, lo bloqueamos directamente
                    $res['status'] = false; 
                    $res['bloqueo'] = true;
                    $res['mensaje'] = "No cumples con los requisitos. No se encontró una matrícula activa para evaluar tus asignaturas.";
                }
                break;
            case '10': // SUSTENTACIÓN DE TESIS TRABAJO DE GRADO
                $egresado = $sivireno->ObtenerEgresado($id_estu);
                if ($egresado) {
                    $res['status'] = true;
                    $res['mensaje'] = "Requisitos cumplidos. Puede proceder con su solicitud de Sustentación del informe de Tesis.";
                } else {
                    $res['status'] = false;
                    $res['mensaje'] = "No cumple con los requisitos para solicitar la Sustentación de Tesis.";
                    $res['bloqueo'] = true;  
                }
            break;
            case '11': // SUSTENTACIÓN DE TESIS SUFICIENCIA
                $egresado = $sivireno->ObtenerEgresado($id_estu);
                if ($egresado) {
                    $res['status'] = true;
                    $res['mensaje'] = "Requisitos cumplidos. Puede proceder con su solicitud de Sustentación de Tesis de Suficiencia.";
                } else {
                    $res['status'] = false;
                    $res['mensaje'] = "No cumple con los requisitos para solicitar este tramite.";
                    $res['bloqueo'] = true;  
                }
            break;

            default:
                $res['status'] = true; // trámites que quizás no requieren validación
                $res['mensaje'] = "Trámite sin restricciones académicas.";
                break;
        }
    } else {
        $res['mensaje'] = "No se recibió el ID del trámite.";
    }

    // ÚNICO echo en todo el proceso:
    echo json_encode($res);
    break;
         
       
}