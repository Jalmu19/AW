<?php

abstract class Tabla {
    protected $columnas;
    protected $datos;

    public function __construct($columnas, $datos) {
        $this->columnas = $columnas;
        $this->datos = $datos;
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

    private function generaCabecera() {
        $html = "<thead><tr>";
        foreach ($this->columnas as $titulo) {
            $html .= "<th>" . htmlspecialchars($titulo) . "</th>";
        }
        $html .= "<th>Acciones</th>"; // Columna fija para botones
        $html .= "</tr></thead>";
        return $html;
    }

    private function generaFila($fila) {
        $html = "<tr>";
        foreach ($this->columnas as $campo => $titulo) {
            $valor = $fila[$campo] ?? '';
            $html .= "<td>" . $this->formateaContenido($campo, $valor, $fila) . "</td>";
        }
        $html .= "<td>" . $this->generaAcciones($fila) . "</td>";
        $html .= "</tr>";
        return $html;
    }

    // Estos métodos se pueden sobrescribir para personalizar cada tabla
    protected function formateaContenido($campo, $valor, $fila) {
        return htmlspecialchars($valor);
    }

    abstract protected function generaAcciones($fila);
}