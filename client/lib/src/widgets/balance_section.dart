// ignore_for_file: library_private_types_in_public_api

import 'package:client/src/services/api_service.dart';
import 'package:flutter/material.dart';

// ignore: use_key_in_widget_constructors
class BalanceSection extends StatefulWidget {
  final String token; //Ajouter le token pour l'authentification
  const BalanceSection({super.key, required this.token});

  @override
  _BalanceSectionState createState() => _BalanceSectionState();
}

class _BalanceSectionState extends State<BalanceSection> {
  bool _isBalanceVisible = true;
  // final double _balance = 15000.0;
  // double? _balance; // Le solde de l'utilisateur.
  String? _balance;

  @override
  void initState() {
    super.initState();
    _fetchBalance();
  }

  Future<void> _fetchBalance() async {
    try {
      var response =
          await ApiService(token: widget.token).get('/clients/balance');

      if (response != null && response['balance'] != null) {
        setState(() {
          _balance = response['balance'];
        });
      }
    } catch (e) {
      // ignore: avoid_print
      print("Erreur lors de la récupération du solde : $e");
      setState(() {
        _balance = null;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      color: Colors.blue,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            'Solde : ${_isBalanceVisible ? '$_balance Fr' : '****'}',
            style: const TextStyle(color: Colors.white, fontSize: 20),
          ),
          IconButton(
            icon: Icon(
              _isBalanceVisible ? Icons.visibility : Icons.visibility_off,
              color: Colors.white,
            ),
            onPressed: () {
              setState(() {
                _isBalanceVisible = !_isBalanceVisible;
              });
            },
          ),
        ],
      ),
    );
  }
}
