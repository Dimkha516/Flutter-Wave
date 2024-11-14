// lib/src/services/auth_service.dart
import 'package:http/http.dart' as http;

class AuthService {
  final String token;

  AuthService({required this.token});

  Future<void> logout() async {
    // final url = Uri.parse('http://192.168.11.56:8000/api/v1/clients/logout');
    final url = Uri.parse('http://192.168.1.66:8000/api/v1/clients/logout');
    // final url = Uri.parse('http://192.168.43.77:8000/api/v1/clients/logout');
    final response = await http.post(
      url,
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      // ignore: avoid_print
      print("Déconnexion réussie");
    } else {
      throw Exception('Erreur lors de la déconnexion');
    }
  }
}
