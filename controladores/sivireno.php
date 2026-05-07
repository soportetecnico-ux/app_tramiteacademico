<?php

session_start();

require_once(__DIR__ . '/../modelos/Sivireno.php');

$sivireno = new Sivireno();

$Date = new DateTime();
$Date->setTimezone(new DateTimeZone('America/Lima'));
$fech_crea = $Date->format("Y-m-d H:i:s");

switch ($_GET["op"]) {

    case 'verificarTramite':
    if (!isset($_SESSION)) { session_start(); }
    $id_estu = $_SESSION['id_estu'];
    
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
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Certificado de Estudios.";
                }
            break;
            case '2': // Constancia de Matrícula
                $actual = $sivireno->semestreDatos($id_estu);   
                if ($actual && !empty($actual['id_semestre'])) {
                    $res['status'] = true;
                    $res['mensaje'] = "Estás matriculado en el semestre: " . $actual['nomsemestre'] ;

                }else {
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
                            $res['mensaje'] = "Tu promedio es " . number_format($promedio, 2) . ". No cumples con el mínimo de 14.";
                        }
                    } else {
                        $res['mensaje'] = "No se encontró historial académico previo para validar.";
                    }
                } else {
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
            case '5': //  Record Integral de Notas
                $asignaturas_aprobadas = $sivireno->numAsigAprobadas($id_estu);
                $total_aprobadas = (isset($asignaturas_aprobadas['total_aprobados'])) ? $asignaturas_aprobadas['total_aprobados'] : 0;
                 if ($total_aprobadas >= 1) {
                    $res['status'] = true;
                    $res['mensaje'] = "Cumples con los requisitos minimos. Apto para solicitar el Constancia de Estudios.";
                } else {
                    $res['mensaje'] = "No cumples con los requisitos minimos. Necesitas al menos 1 asignatura aprobada para solicitar el Constancia de Estudios.";
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