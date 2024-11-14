// ignore_for_file: prefer_const_constructors

import 'package:client/src/services/api_service.dart';
import 'package:flutter/material.dart';

class TransactionHistory extends StatefulWidget {
  final ApiService apiService;

  const TransactionHistory({super.key, required this.apiService});

  @override
  // ignore: library_private_types_in_public_api
  _TransactionHistoryState createState() => _TransactionHistoryState();
}

class _TransactionHistoryState extends State<TransactionHistory> {
  List<Map<String, dynamic>> _transactions = [];

  @override
  void initState() {
    super.initState();
    _fetchTransactions();
  }

  Future<void> _fetchTransactions() async {
    try {
      final transactions = await widget.apiService.getUserTransactions();
      setState(() {
        _transactions = transactions
            .map((transaction) => {
                  'type': transaction['type'],
                  'date': transaction['date'],
                  'amount': double.tryParse(transaction['montant']) ?? 0.0,
                })
            .toList();

        // Trier la liste par date, de la plus récente à la plus ancienne
        _transactions.sort((a, b) => b['date'].compareTo(a['date']));
      });
    } catch (e) {
      // Gérer les erreurs de récupération ici, si besoin
      // ignore: avoid_print
      print('Erreur: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return _transactions.isEmpty
        ? Center(
            child: Text(
              'Aucune transaction pour le moment !',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
          )
        : ListView.builder(
            itemCount: _transactions.length,
            itemBuilder: (context, index) {
              final transaction = _transactions[index];
              return ListTile(
                title: Text(transaction['type']),
                subtitle: Text(transaction['date']),
                trailing: Text(
                  '${transaction['amount']} Fr',
                  style: TextStyle(
                    color: Colors.green,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              );
            },
          );
  }
}
