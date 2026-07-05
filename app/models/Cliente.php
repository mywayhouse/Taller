public function buscarPorRtnONombre(string $term): array
{
    $sql = "SELECT id_cliente, nombre, rnt_dni 
            FROM clientes 
            WHERE (rnt_dni LIKE :term1 OR nombre LIKE :term2) AND estado_activo = 1 
            LIMIT 10";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'term1' => $term . '%',
        'term2' => '%' . $term . '%'
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}