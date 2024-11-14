import 'package:flutter/material.dart';

class ActionButtons extends StatelessWidget {
  final Function onTransferPressed;
  const ActionButtons({super.key, required this.onTransferPressed});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        _buildActionButton(Icons.send, 'Transfert', onTransferPressed),
        _buildActionButton(Icons.payment, 'Paiements', () {}),
        _buildActionButton(Icons.phone_android, 'Crédit', () {}),
      ],
    );
  }

  Widget _buildActionButton(IconData icon, String label, Function onPressed) {
    return GestureDetector(
      onTap: () => onPressed(),
      child: Column(
        children: [
          Icon(icon, size: 40, color: Colors.blue),
          const SizedBox(height: 5),
          Text(label),
        ],
      ),
    );
  }
}
