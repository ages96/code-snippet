<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoanInstallment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get_all_installments() {
        $this->db->select('installments.*, loans.loan_number');
        $this->db->from('installments');
        $this->db->join('loans', 'installments.loan_id = loans.id');
        $this->db->order_by('installments.due_date', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_installment($id) {
        $this->db->select('installments.*, loans.loan_number');
        $this->db->from('installments');
        $this->db->join('loans', 'installments.loan_id = loans.id');
        $this->db->where('installments.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function create_installment() {
        $data = array(
            'loan_id' => $this->input->post('loan_id', TRUE),
            'installment_amount' => $this->input->post('installment_amount', TRUE),
            'due_date' => $this->input->post('due_date', TRUE)
        );
        return $this->db->insert('installments', $data);
    }

    public function update_installment($id) {
        $data = array(
            'installment_amount' => $this->input->post('installment_amount', TRUE),
            'due_date' => $this->input->post('due_date', TRUE)
        );
        $this->db->where('id', $id);
        return $this->db->update('installments', $data);
    }

    public function delete_installment($id) {
        return $this->db->delete('installments', array('id' => $id));
    }
}
