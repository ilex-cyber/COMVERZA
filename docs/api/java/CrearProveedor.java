import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

/**
 * COMVERZA · Prueba del método POST (create) con Java 11+
 * sobre la API de proveedores.
 *
 *   java CrearProveedor.java
 *
 * No requiere compilar ni dependencias: usa java.net.http.HttpClient.
 */
public class CrearProveedor {

    static final String API = "http://localhost:8082/api/v1/proveedores";

    public static void main(String[] args) throws Exception {
        long sufijo = System.currentTimeMillis() % 1000;

        String json = """
            {
              "razon_social": "Aceros y Herrajes del Norte S.A. de C.V.",
              "rfc": "AHN260101%03d",
              "contacto": "Ricardo Treviño",
              "email": "ventas%d@acerosnorte.mx",
              "telefono": "8187654321",
              "ciudad": "Monterrey",
              "sitio_web": "https://www.acerosnorte.mx",
              "calificacion": 5,
              "activo": true
            }
            """.formatted(sufijo, sufijo);

        HttpClient cliente = HttpClient.newBuilder().connectTimeout(Duration.ofSeconds(10)).build();

        HttpRequest peticion = HttpRequest.newBuilder()
                .uri(URI.create(API))
                .header("Content-Type", "application/json")
                .header("Accept", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(json))
                .build();

        System.out.println("POST " + API);
        System.out.println("Cuerpo enviado:" + json);

        HttpResponse<String> respuesta = cliente.send(peticion, HttpResponse.BodyHandlers.ofString());

        System.out.println("Código de respuesta: " + respuesta.statusCode()
                + (respuesta.statusCode() == 201 ? " (Created)" : ""));
        System.out.println("Respuesta del servidor:");
        System.out.println(respuesta.body());
    }
}
