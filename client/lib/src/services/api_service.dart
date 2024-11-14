import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  // final String _baseUrl = 'http://192.168.43.77:8000/api/v1'; // Vivo66
  // final String _baseUrl = 'http://192.168.11.56:8000/api/v1'; //SONATEL
  final String _baseUrl = 'http://192.168.1.66:8000/api/v1'; // SocWifi

  final String? token; // Ajoutez un token pour l'authentification
  ApiService({required this.token});

  Future<String> login(String phone, String password) async {
    const endpoint = '/clients/login';
    final data = {
      'telephone': phone,
      'mot_de_passe': password,
    };

    final response = await post(endpoint, data);

    if (response != null && response is Map && response.containsKey('token')) {
      return response['token'];
    } else {
      throw Exception('Erreur d\'authentification');
    }
  }

  // GET request
  Future<dynamic> get(String endpoint) async {
    final url = Uri.parse('$_baseUrl$endpoint');
    try {
      final response = await http.get(url, headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        throw Exception('Erreur ${response.statusCode}: ${response.body}');
      }
    } catch (e) {
      throw Exception('Erreur de connexion: $e');
    }
  }

  // POST request:
  Future<dynamic> post(String endpoint, Map<String, dynamic> data) async {
    try {
      final headers = {
        'Content-Type': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

      final response = await http.post(
        Uri.parse('$_baseUrl$endpoint'),
        headers: headers,
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Erreur ${response.statusCode}: ${response.body}');
      }
    } catch (e) {
      throw Exception('Erreur de connexion: $e');
    }
  }

  Future<List<dynamic>> getUserTransactions() async {
    final response = await http.get(
      // Uri.parse('$_baseUrl/transactions/historique'),
      // Uri.parse('http://192.168.43.77:8000/api/v1/transactions/historique'),
      // Uri.parse('http://192.168.11.56:8000/api/v1/transactions/historique'),
      // Uri.parse('http://192.168.1.66:8000/api/v1/transactions/historique'),
      // Uri.parse('$_baseUrl/transactions/historique'),
      Uri.parse('$_baseUrl/transactions/historique'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Erreur lors de la récupération des transactions');
    }
  }

  Future<List<dynamic>> getService() async {
    final response = await http
        // .get(Uri.parse('$_baseUrl/services'), headers: {
        .get(Uri.parse('$_baseUrl/services'), headers: {
      // .get(Uri.parse('$_baseUrl/services'), headers: {
      'Authorization': 'Bearer $token',
    });

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Erreur lors de la récupération des services');
    }
  }

  Future<Map<String, dynamic>> sendMultipleTransactions(
      List<String> phoneNumbers, double amount) async {
    final url = Uri.parse('$_baseUrl/transactions/send-multiple');
    final response = await http.post(
      url,
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'phone_numbers': phoneNumbers,
        'amount': amount,
      }),
    );
    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Echec de l\'envoi multiple: ${response.body}');
    }
  }

  // Récupération des transactions planifiées de l'utilisateur:
  Future<List<dynamic>> getScheduledTransactions() async {
    final url = Uri.parse('$_baseUrl/transactions/sheduled');

    final response = await http.get(
      url,
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception(
          'Erreur lors de la récupération des transactions planifiées: ${response.body}');
    }
  }

  // Ajouter une nouvelle transaction planifiée:
  // Ajouter une nouvelle transaction planifiée
  Future<Map<String, dynamic>> addScheduledTransaction(
      String recipient, double amount, String date,
      {String frequency = 'monthly'}) async {
    final url = Uri.parse('$_baseUrl/transactions/planification');

    try {
      final response = await http.post(
        url,
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'numero_destinataire': recipient,
          'montant': amount,
          'date': date,
          'frequence': frequency,
        }),
      );

      if (response.statusCode == 201) {
        // Retourner la réponse si la transaction a été créée avec succès
        return jsonDecode(response.body);
      } else {
        // Décoder le corps de la réponse pour récupérer le message d'erreur détaillé
        final errorResponse = jsonDecode(response.body);
        throw Exception(errorResponse['message'] ?? 'Erreur inconnue');
      }
    } catch (error) {
      // Gérer les erreurs d'exception
      throw Exception(
          'Erreur lors de l\'ajout de la transaction planifiée: $error');
    }
  }

  Future<void> cancelScheduledTransaction(int transactionId) async {
    final url =
        Uri.parse('$_baseUrl/transactions/cancelShedule/{transactionId}');

    final response = await http.delete(
      url,
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode != 200) {
      throw Exception(
          'Erreur lors de l\'annulation de la transaction planifiée: ${response.body}');
    }
  }
}
