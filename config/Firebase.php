<?php
// ============================================================
// firebase.php — Conexión a Firebase (Singleton)
// ============================================================
// Inicializa el SDK de Firebase (kreait/firebase-php) usando
// las credenciales de la cuenta de servicio (JSON) descargada
// desde la Consola de Firebase.
//
// Proporciona acceso único a:
//   - Firebase Auth (Autenticación de usuarios)
//   - Cloud Firestore (Base de datos NoSQL)
//   - Realtime Database (Base de datos en tiempo real)
//
// REQUISITO: Colocar el archivo JSON de la cuenta de servicio
// en:   /storage/app/firebase-credentials.json
// ============================================================

namespace Config;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;
use Exception;

class Firebase
{
    /**
     * Instancia única del Factory de Firebase.
     * @var Factory|null
     */
    private static ?Factory $factory = null;

    /**
     * Instancia única del servicio de Autenticación.
     * @var Auth|null
     */
    private static ?Auth $auth = null;

    /**
     * Instancia única del servicio de Firestore.
     * @var Firestore|null
     */
    private static ?Firestore $firestore = null;

    /**
     * Instancia única del servicio de Realtime Database.
     * @var Database|null
     */
    private static ?Database $realtimeDatabase = null;

    /**
     * Ruta al archivo JSON de credenciales de la cuenta de servicio.
     * Se espera en /storage/app/firebase-credentials.json
     */
    private const CREDENTIALS_PATH = ROOT . '/storage/app/firebase-credentials.json';

    /**
     * URL del proyecto de Firebase (Realtime Database).
     * Cambiar por la URL real del proyecto en la Consola de Firebase.
     */
    private const DATABASE_URI = 'https://taller-mecanico-default-rtdb.firebaseio.com';

    // ----------------------------------------------------------
    // Constructor y clonación privados (patrón Singleton)
    // ----------------------------------------------------------
    private function __construct() {}
    private function __clone() {}

    /**
     * Obtiene o crea la instancia única del Factory de Firebase.
     *
     * @return Factory
     * @throws Exception Si no encuentra el archivo de credenciales.
     */
    private static function getFactory(): Factory
    {
        if (self::$factory === null) {
            // --------------------------------------------------
            // Validar que el archivo de credenciales exista
            // --------------------------------------------------
            if (!file_exists(self::CREDENTIALS_PATH)) {
                throw new Exception(
                    'Archivo de credenciales de Firebase no encontrado en: '
                    . self::CREDENTIALS_PATH
                    . '. Descárgalo desde Consola Firebase > Ajustes del proyecto '
                    . '> Cuentas de servicio > Generar nueva clave privada.'
                );
            }

            try {
                // --------------------------------------------------
                // Crear el Factory con las credenciales y servicios
                // --------------------------------------------------
                self::$factory = (new Factory)
                    ->withServiceAccount(self::CREDENTIALS_PATH)
                    ->withDatabaseUri(self::DATABASE_URI);
            } catch (FirebaseException $e) {
                error_log('Error al inicializar Firebase: ' . $e->getMessage());
                throw new Exception(
                    'Error al conectar con Firebase. Verifica el archivo de credenciales.'
                );
            }
        }

        return self::$factory;
    }

    /**
     * Obtiene la instancia Singleton de Firebase Authentication.
     * Permite gestionar usuarios (registro, login, verificación)
     * sin depender de la tabla MySQL.
     *
     * Uso en un controlador:
     *   $auth = \Config\Firebase::auth();
     *   $user = $auth->signInWithEmailAndPassword($email, $password);
     *
     * @return Auth
     */
    public static function auth(): Auth
    {
        if (self::$auth === null) {
            self::$auth = self::getFactory()->createAuth();
        }
        return self::$auth;
    }

    /**
     * Obtiene la instancia Singleton de Cloud Firestore.
     * Base de datos NoSQL para almacenar documentos y colecciones.
     * Útil para datos flexibles que no requieren esquema fijo.
     *
     * Uso en un modelo:
     *   $db = \Config\Firebase::firestore()->database();
     *   $docRef = $db->collection('vehiculos')->document('ABC123');
     *   $snapshot = $docRef->snapshot()->data();
     *
     * @return Firestore
     */
    public static function firestore(): Firestore
    {
        if (self::$firestore === null) {
            self::$firestore = self::getFactory()->createFirestore();
        }
        return self::$firestore;
    }

    /**
     * Obtiene la instancia Singleton de Realtime Database.
     * Sincronización en tiempo real ideal para:
     *   - Estados de órdenes de reparación
     *   - Notificaciones push
     *   - Seguimiento en vivo
     *
     * Uso:
     *   $db = \Config\Firebase::realtimeDatabase();
     *   $ref = $db->getReference('ordenes/activas');
     *   $data = $ref->getValue();
     *
     * @return Database
     */
    public static function realtimeDatabase(): Database
    {
        if (self::$realtimeDatabase === null) {
            self::$realtimeDatabase = self::getFactory()->createDatabase();
        }
        return self::$realtimeDatabase;
    }
}
