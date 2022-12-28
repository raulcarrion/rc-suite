<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 */

/**
 * @since      1.0.0
 * @package    Rc_Suite
 * @subpackage Rc_Suite/includes
 * @author     Raúl Carrión <hola@raulcarrion.com>
 */
class Rc_Suite_Reemplazador {

    private $reemplazos = array();

	/**
	 * @since    1.0.0
	 */
	public function __construct($reemplazos="") {
        $this->reemplazos = $reemplazos;
    }


    /**
     *  Obtiene los reemplazos mediante un fichero CSV 
     * 
	 * @since    1.0.0
	 * @var      $file   ruta fichero CSV
     */

    public function set_replaces_by_csv($file)
    {
        if (($fichero = fopen($file,"r")) !== FALSE)
		{
			$this->reemplazos = array();

            while (($campos_csv = fgetcsv($fichero,0,";")) !== FALSE)
			{
                $this->reemplazos [] = array($campos_csv[0] => array($campos_csv[1],$campos_csv[2],$campos_csv[3]));	
                $num +=1;
            }
            
            return $num;
		}	
		else 
		{
            return false;
		}
    }

    /**
     *  devuelve la informacion de reemplazos de un fichero
     * 
	 * @since    1.0.0
	 * @var      $file   ruta fichero CSV
     */

    public function get_replaces_by_csv($file)
    {
        if (($fichero = fopen($file,"r")) !== FALSE)
		{
			$this->reemplazos = array();
            $num=0;

            while (($campos_csv = fgetcsv($fichero,0,";")) !== FALSE)
			{
                $this->reemplazos [] = array($campos_csv[0] => array($campos_csv[1],$campos_csv[2],$campos_csv[3]));	
                $num +=1;
            }
            
            return $this->reemplazos;
		}	
		else 
		{
            return false;
		}
    }

    /**
     *  Obtiene el numero de instancias existentes en BBDD 
     * 
	 * @since    1.0.0
	 * @var      $objet   el objeto donde se van a realizar los reemplazos
     */

    public function get_num_reemplazos()
    {
        if (!empty($this->reemplazos))
        {
            $retorno = array();

            foreach ($this->reemplazos as $reemplazo)
            {
                foreach ($reemplazo as $key => $value)
                {
                    $retorno_sql = $this->sql_get_num_reemplazos($key, $value[0], $value[1]);
                    array_push($retorno,array("Tabla" => $key, "Campo" => $value[0], "Cadena" => esc_html($value[1]), "Coincidencias" => $retorno_sql));
                }
            }
        }
        else 
        {
            $retorno = false;    
        }

        return $retorno;
    }

    /**
     *  Realizar los replaces en BBDD
     * 
	 * @since    1.0.0
	 * @var      $objet   el objeto donde se van a realizar los reemplazos
     */

    public function do_replaces()
    {
        if (!empty($this->reemplazos))
        {
            $retorno = array();
            
            foreach ($this->reemplazos as $reemplazo)
            {
                foreach ($reemplazo as $key => $value)
                {
                    $retorno_sql = $this->sql_reemplazar($key, $value[0], $value[1], $value[2]);
                    array_push($retorno,array("Tabla" => $key, "Campo" => $value[0], "Cadena" => esc_html($value[1]), "Afectadas" => $retorno_sql));
                }
            }
        }
        else 
        {
            $retorno = false;    
        }

        return $retorno;
    }
    
    /**
     *  Ejecuta la SQL para obtener el numero de instancias existentes en BBDD 
     * 
	 * @since    1.0.0
	 * @var      $objet   el objeto donde se van a realizar los reemplazos
     */

    private function sql_get_num_reemplazos ($tabla, $columna, $cadena_origen)
    {
        global $wpdb;

        return ($wpdb->get_var($wpdb->prepare(
            "SELECT
                COUNT(".$columna.")
            FROM
                `{$wpdb->prefix}". $tabla ."`
            WHERE
                ".$columna." LIKE %s;",
            '%' . $wpdb->esc_like($cadena_origen) . '%'
        )));
    }
    
    /**
     *  Ejecuta la SQL para realizar los reemplazos en BBDD
     * 
	 * @since    1.0.0
	 * @var      $objet   el objeto donde se van a realizar los reemplazos
     */

    private function sql_reemplazar ($tabla, $columna, $cadena_origen, $cadena_destino)
    {
        global $wpdb;

        return ($wpdb->query($wpdb->prepare(
            
            "UPDATE `{$wpdb->prefix}". $tabla ."`
            SET ".$columna." = REPLACE(".$columna.", '$cadena_origen', '$cadena_destino')
            WHERE ".$columna." LIKE %s;", 
            '%' . $wpdb->esc_like($cadena_origen) . '%'
        )));
            
    }

    /**
	 * @since    1.0.0
	 */
	public function get_table($index) {
    
        return is_null($this->reemplazos) || !is_array($this->reemplazos) ? "" : array_keys($this->reemplazos[$index])[0] ;
    }

    /**
	 * @since    1.0.0
	 */
	public function get_field($index) {

        return is_null($this->reemplazos) || !is_array($this->reemplazos) ? "" : $this->reemplazos[$index][array_keys($this->reemplazos[$index])[0]][0];
     }

   /**
	 * @since    1.0.0
	 */
	public function get_source($index) {

        return is_null($this->reemplazos) || !is_array($this->reemplazos) ? "" : $this->reemplazos[$index][array_keys($this->reemplazos[$index])[0]][1];
     }

   /**
	 * @since    1.0.0
	 */
	public function get_destination($index) {

        return is_null($this->reemplazos) || !is_array($this->reemplazos) ? "" : $this->reemplazos[$index][array_keys($this->reemplazos[$index])[0]][2];
    }    
}