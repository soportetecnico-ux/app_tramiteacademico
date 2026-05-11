<?php

require_once(__DIR__ . '/../config/conexion.php');

class Sivireno
{
    public function __construct() {}

        public function semestreDatos($id_estu)
        {
            $sql = "SELECT s.id_semestre, s.nomsemestre, d.situacion, d.id_plan, f.ciclo_ficham
                    FROM `ficha_matricula` f
                    INNER JOIN `semestre` s ON f.id_semestre = s.id_semestre
                    INNER JOIN `detestudiante` d ON f.id_estu = d.id_estu
                    WHERE f.id_estu= $id_estu
                    AND s.tiposemestre = 'r'  
                    AND s.activo='SI'
                    AND d.activo='SI'
                    AND f.anulado = 0
                    AND f.borrado = 0
                    LIMIT 1";
            return ejecutarConsultaSimpleFila2($sql); // Esto quita el error de Intelephense
        }

        public function oldSemestre()
        {
            $sql = "SELECT 
                        s.id_semestre, 
                        s.nomsemestre
                    FROM semestre s                 
                    WHERE s.tiposemestre = 'r'  
                    ORDER BY s.id_semestre DESC             
                    LIMIT 1 OFFSET 1;";
            return ejecutarConsulta2($sql);
        }
        public function calcularPromedio($id_estu, $id_semestre) {
            $sql = "SELECT AVG(profinal_record) as promedio 
                    FROM asignacion_estudiante 
                    WHERE id_estu = '$id_estu' AND id_semestre = '$id_semestre'";
            return ejecutarConsultaSimpleFila2($sql);
        }

        public function obtenerCreditosPermitidos($id_estu, $id_semestre, $id_plan) {
            $sql = "SELECT cc.credito 
                    FROM ficha_matricula f
                    INNER JOIN detestudiante d ON f.id_estu = d.id_estu
                    INNER JOIN creditos_ciclo cc ON d.id_plan = cc.id_plan AND f.ciclo_ficham = cc.ciclo
                    WHERE f.id_estu = '$id_estu' AND f.id_semestre = '$id_semestre' AND d.id_plan = '$id_plan' AND f.anulado=0 AND f.borrado=0";
            return ejecutarConsultaSimpleFila2($sql);
        }

        public function numAsigAprobadas($id_estu) {
            $sql = "SELECT COUNT(*) as total_aprobados 
                        FROM asignacion_estudiante 
                        WHERE id_estu = '$id_estu' 
                        AND profinal_record >= 11";
            return ejecutarConsultaSimpleFila2($sql);
        }

        public function obtenerSemestreCulminado($id_estu) {
            $sql = "SELECT fm.id_semestre, s.nomsemestre 
                    FROM ficha_matricula fm
                    INNER JOIN semestre s ON fm.id_semestre = s.id_semestre
                    WHERE fm.id_estu = '$id_estu' 
                    AND s.activo = 'no' 
                    AND s.tiposemestre = 'r' 
                    AND fm.anulado = 0 
                    AND fm.borrado = 0
                    ORDER BY s.id_semestre DESC 
                    LIMIT 1";
             return ejecutarConsultaSimpleFila2($sql);
        }
        public function obtenerOrdenMerito($id_estu, $id_semestre_old) {
            $sql = "SELECT om as 'orden de merito'
                    FROM ficha_matricula
                    WHERE id_estu = '$id_estu' 
                    AND id_semestre = '$id_semestre_old'
                    AND anulado = 0 
                    AND borrado = 0";
            return ejecutarConsultaSimpleFila2($sql);
        }
        public function ObtenerEgresado($id_estu){
            $sql = "SELECT * FROM `egresados`
             WHERE id_estu=$id_estu;";
            return ejecutarConsultaSimpleFila2($sql);
        }
        public function verificarPractica($id_estu, $id_semestre) {
            $sql = "SELECT COUNT(*) AS total_practicas 
                    FROM ficha_matricula fm
                    INNER JOIN asignacion_estudiante ae ON fm.id_ficham = ae.id_ficham 
                    INNER JOIN asignatura a ON ae.id_asi = a.id_asi 
                    WHERE fm.id_estu = '$id_estu' 
                    AND fm.id_semestre = '$id_semestre'  
                    AND (UPPER(a.nom_asi) LIKE '%PR%CTICA%PRE%PROFESIONAL%' 
                        OR UPPER(a.nom_asi) LIKE '%PR%CTICA%PREPROFESIONAL%')";
            
            return ejecutarConsultaSimpleFila2($sql);
        }
}