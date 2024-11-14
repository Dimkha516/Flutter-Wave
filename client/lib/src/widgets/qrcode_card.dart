import 'package:flutter/material.dart';

class QrCodeCard extends StatelessWidget {
  const QrCodeCard({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 20),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(8),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 2,
            blurRadius: 5,
          ),
        ],
      ),
      child: const Column(
        children: [
          Text('Votre QR Code'),
          SizedBox(height: 10),
          Icon(Icons.qr_code, size: 100, color: Colors.blue),
        ],
      ),
    );
  }
}
