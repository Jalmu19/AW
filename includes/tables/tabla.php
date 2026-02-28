<?php

abstract class Tabla {
    protected $columnas;
    protected $datos;
    protected $mostrarAcciones;

    public function __construct($columnas, $datos, $mostrarAcciones) {
        $this->columnas = $columnas;
        $this->datos = $datos;
        $this->mostrarAcciones = $mostrarAcciones;
    }

    private function generaCabecera() {
        $html = "<thead><tr>";
        foreach ($this->columnas as $titulo) {
            $html .= "<th>" . htmlspecialchars($titulo) . "</th>";
        }
        // Solo añade el <th> si hay acciones
        if ($this->mostrarAcciones) {
            $html .= "<th>Acciones</th>";
        }
        $html .= "</tr></thead>";
        return $html;
    }

    // Método que genera el HTML completo
    public function genera() {
        $html = "<table>";
        $html .= $this->generaCabecera();
        $html .= "<tbody>";
        
        if ($this->datos instanceof mysqli_result) {
            while ($fila = $this->datos->fetch_assoc()) {
                $html .= $this->generaFila($fila);
            }
        } else {
            foreach ($this->datos as $fila) {
                $html .= $this->generaFila($fila);
            }
        }

        $html .= "</tbody></table>";
        return $html;
    }

    private function generaFila($fila) {
        $html = "<tr>";
        foreach ($this->columnas as $campo => $titulo) {
            $valor = $fila[$campo] ?? '';
            $html .= "<td>" . $this->formateaContenido($campo, $valor, $fila) . "</td>";
        }
        // Solo añade el <td> si hay acciones
        if ($this->mostrarAcciones) {
            $html .= "<td>" . $this->generaAcciones($fila) . "</td>";
        }
        $html .= "</tr>";
        return $html;
    }

    protected function formateaContenido($campo, $valor, $fila) {
        return htmlspecialchars($valor);
    }

    // Lo dejamos vacío por defecto para las tablas que no lo usen
    protected function generaAcciones($fila) {
        return "";
    }
}